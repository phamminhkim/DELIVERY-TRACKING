<?php

namespace App\Repositories\Master;

use App\Models\Master\MenuRouter;
use App\User;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Utilities\MenuUtility;
use App\Utilities\NestedSetSync;
use App\Utilities\RedisUtility;
use Illuminate\Support\Facades\Validator;

class MenuRouterRepository extends RepositoryAbs
{
    public function getConfigMenus()
    {
        try {
            $all_menus = MenuRouter::where('is_active', 1)->with(['route', 'roles'])->orderBy('parent_id')->orderBy('order')->get();

            $list_menus = array();
            $tree_menus = array();
            $root_tree_menus = array();

            foreach ($all_menus as $menu) {
                $parent_id = $menu['parent_id'];
                $child_id = $menu['id'];
                if (!array_key_exists($parent_id, $tree_menus)) {
                    $tree_menus[$parent_id] = array();
                }
                $tree_menus[$parent_id][] = $child_id;

                if ($parent_id == 0) {
                    $root_tree_menus[] = $child_id;
                }
                $menu->role_ids = $menu->roles->pluck('id')->toArray();
                unset($menu->roles);
                $list_menus[$menu->id] = $menu;
            }

            $result = array();
            $result['list_menus'] = $list_menus;
            $result['tree_menus'] = $tree_menus;
            $result['root_tree_menus'] = $root_tree_menus;

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function saveConfigMenus()
    {
        try {
            $data = $this->request->all();

            $menus =  MenuUtility::convertTreeToArray($data, 0);

            $i = 0;
            foreach ($menus as $row) {
                $menu = MenuRouter::find($row['id']);
                $menu->route_id = $row['route_id'] ?? null;
                $menu->parent_id = $row['parent_id'];
                $menu->roles()->sync($row['role_ids']);
                $menu->order = ++$i;
                $menu->title = $row['title'];
                $menu->link = $row['link'];
                $menu->query_string = $row['query_string'] ?? '';
                $menu->icon = $row['icon'] ?? '';
                $menu->left = 0;
                $menu->right = 0;
                $menu->is_active = true;
                $menu->save();
            }
            $transformer = new NestedSetSync();
            $transformer->traverseUpdate();
            RedisUtility::deleteByCategory('menu-tree');

            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function deleteConfigMenu($id)
    {
        try {
            $menu = MenuRouter::find($id);
            if ($menu) {
                $menu->delete();
                $transformer = new NestedSetSync();
                $transformer->traverseUpdate();
                RedisUtility::deleteByCategory('menu-tree');
                return true;
            }
            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
