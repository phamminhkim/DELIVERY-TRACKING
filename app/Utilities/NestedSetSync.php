<?php

namespace App\Utilities;

use App\Models\Master\MenuRouter;
use App\Models\shared\Menu;

class NestedSetSync
{
    private $menus_map;
    private $current_count;

    public function __construct()
    {
        $this->menus_map = array();
        $this->current_count = 1;

        $menus = MenuRouter::select('id', 'parent_id', 'order')->orderBy('parent_id')->orderBy('order')->get();
        foreach ($menus as $menu) {
            $parent_id = $menu['parent_id'];
            $child_id = $menu['id'];
            if (!array_key_exists($parent_id, $this->menus_map)) {
                $this->menus_map[$parent_id] = array();
            }
            $this->menus_map[$parent_id][] = $child_id;
        }
    }

    public function traverseUpdate($menu_id = 0)
    {
        $left = $this->current_count;
        $this->current_count++;

        $menu_children = $this->getMenuChildren($menu_id);
        if ($menu_children) {
            foreach ($menu_children as $menu_child) {
                $this->traverseUpdate($menu_child);
            }
        }
        $right = $this->current_count;
        $this->current_count++;
        $this->updateMenu($menu_id, $left, $right);
    }

    private function getMenuChildren($menu_id)
    {
        if (array_key_exists($menu_id, $this->menus_map)) {
            return $this->menus_map[$menu_id];
        }
        return null;
    }

    private function updateMenu($menu_id, $left, $right)
    {
        $menu = MenuRouter::find($menu_id);
        if ($menu) {
            $menu->left = $left;
            $menu->right = $right;
            $menu->save();
        }
    }
}
