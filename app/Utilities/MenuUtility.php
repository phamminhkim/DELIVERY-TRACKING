<?php

namespace App\Utilities;

use App\Models\Master\MenuRouter;
use App\Models\System\Route;
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
                    $new_menu->route_id = $menu['route_id'] ?? null;
                    $new_menu->title = $menu['title'];
                    $new_menu->parent_id = $parent_id;
                    $new_menu->link = $menu['link'];
                    $new_menu->query_string = $menu['query_string'] ?? '';
                    $new_menu->icon = $menu['icon'] ?? '';
                    $new_menu->order = 0;
                    $new_menu->left = 0;
                    $new_menu->right = 0;
                    $new_menu->is_active = true;
                    $new_menu->save();

                    $menu['id'] = $new_menu->id;
                }
                if (isset($menu['children'])) {
                    $tree_sub_array = MenuUtility::convertTreeToArray($menu['children'], $menu['id']);
                }
                $tree_array[] = array(
                    'id' => $menu['id'],
                    'route_id' => $menu['route_id'] ?? null,
                    'parent_id' => intval($parent_id),
                    'title' => $menu['title'],
                    'link' => $menu['link'],
                    'query_string' => $menu['query_string'] ?? '',
                    'icon' => $menu['icon'] ?? '',
                );
                $tree_array = array_merge($tree_array, $tree_sub_array);
            } catch (\Throwable $th) {
                dd($th, $menu);
            }
        }

        return $tree_array;
    }
    public static function getUserMenus($user_id) {
        return MenuRouter::where('is_active', true)->get();
    }
    public static function getMenusAndRoutesForUser($user_id)
    {
        $menus = array();
        $routes = array();
        $cache_menus = RedisUtility::getByCategory('menu-tree', $user_id, false);
        $cache_routes = RedisUtility::getByCategory('routes', $user_id, true);
        if ($cache_menus && $cache_routes) {
            $menus = $cache_menus;
            $routes = $cache_routes;
        } else {
            $raw_role_menus = MenuUtility::getUserMenus($user_id);
            $routes = Route::whereIn('id', $raw_role_menus->pluck('route_id')->toArray())->get();

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
            RedisUtility::saveWithCategoryExpire('routes', $user_id, $routes, 3600);
        }

        return array('menus' => $menus, 'routes' => $routes);
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
