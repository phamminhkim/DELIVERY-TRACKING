<?php

namespace App\Utilities;

use App\Models\Master\MenuRouter;
use App\User;

class MenuUtility
{
    public static function convertTreeToArray($menus, $parent_id = 0)
    {
        $tree_array = array();
        foreach ($menus as $menu) {
            try {
                $tree_sub_array = array();
                if (!isset($menu['id'])) {
                    $new_menu = new MenuRouter();
                    $new_menu->title = $menu['title'];
                    $new_menu->parent_id = $parent_id;
                    $new_menu->link = $menu['link'];
                    $new_menu->icon = $menu['icon'];
                    $new_menu->order = 0;
                    $new_menu->save();

                    $menu['id'] = $new_menu->id;
                }
                if (isset($menu['children'])) {
                    $tree_sub_array = MenuUtility::convertTreeToArray($menu['children'], $menu['id']);
                }
                $tree_array[] = array(
                    'id' => $menu['id'],
                    'parent_id' => intval($parent_id),
                    'title' => $menu['title'],
                    'link' => $menu['link'],
                    'icon' => $menu['icon'],
                );
                $tree_array = array_merge($tree_array, $tree_sub_array);
            } catch (\Throwable $th) {
                dd($th, $menu);
            }
        }

        return $tree_array;
    }

    public static function getMenusForUser($user_id, $request_path)
    {
        $menu_data = array();
        $menu_data['list_menus'] = array();
        $menu_data['current_menu'] = null;
        $menu_data['current_root'] = null;

        $menus = array();
        $cache_menus = RedisUtility::getByCategory('menu-tree', $user_id);
        if ($cache_menus) {
            $menus = $cache_menus;
        } else {
            $raw_role_menus = MenuRouter::all();

            $role_menus = array();
            foreach ($raw_role_menus as $menu) {
                $role_menus[$menu->id] = $menu;
            }

            $module_menus = array();
            foreach ($role_menus as  $role_menu) {
                $menu_tree = MenuUtility::getNestedSetMenuTreeTop($role_menu);

                if (count($menu_tree) > 0) {
                    if (count($menu_tree) > 1) {
                        $menu_root = MenuUtility::getNestedSetMenuRoot($role_menu);
                        if ($menu_root && !in_array($menu_root->id, $module_menus)) {
                            array_push($module_menus, $menu_root->id);
                        }
                    } else {
                        array_push($module_menus, $role_menu->id);
                    }
                }
            }
            $menus = MenuRouter::whereIn('id', $module_menus)->with('children')->orderBy('order')->get();
            foreach ($menus as $menu) {
                $menu = MenuUtility::getRecursiveMenuChildren($menu);
            }

            RedisUtility::saveWithCategoryExpire('menu-tree', $user_id, $menus, 3600);
        }
        $request_path = str_replace('app/', '/', $request_path);
        $current_menu = MenuRouter::where('link', $request_path)->get()->first();
        $current_root_menu = 0;
        if ($current_menu) {
            $current_root_menu = MenuUtility::getNestedSetMenuRoot($current_menu);
        } else {
            $current_menu = new MenuRouter();
            $current_menu->id = 0;
        }
        if (!$current_root_menu) {
            $current_root_menu = new MenuRouter();
            $current_root_menu->id = 0;
        }

        $menu_data['list_menus'] = $menus;
        $menu_data['current_menu'] = $current_menu;
        $menu_data['current_root'] = $current_root_menu;

        return $menu_data;
    }
    public static function getNestedSetMenuTreeTop($menu)
    {
        try {
            $treetop_menus = MenuRouter::where('left', '<=', $menu->left)->where('right', '>=', $menu->right)->get();
        } catch (\Throwable $th) {
            $treetop_menus = array();
        }

        return $treetop_menus;
    }
    public static function getNestedSetMenuRoot($menu)
    {
        try {
            $root_menu = MenuRouter::where('left', '<', $menu->left)->where('right', '>', $menu->right)->orderBy('left')->first();
        } catch (\Throwable $th) {
            $root_menu = array();
        }

        return $root_menu;
    }
    public static function getRecursiveMenuChildren($menu)
    {
        $menu->has_children = count($menu->children) > 0;
        foreach ($menu->children as $child) {
            $child = MenuUtility::getRecursiveMenuChildren($child);
        }

        return $menu;
    }
}
