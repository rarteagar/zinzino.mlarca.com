<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Spatie\Menu\Laravel\Menu as SpatieMenu;
use Spatie\Menu\Link;

class MenuBuilder
{
    public function build($menuName = 'main')
    {
        $menuItems = Menu::with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return $this->buildMenu($menuItems);
    }

    protected function buildMenu($items)
    {
        $menu = SpatieMenu::new()
            ->addClass('nav flex-column')
            ->setActiveFromRequest();

        foreach ($items as $item) {
            if ($this->userCanView($item)) {
                if ($item->hasChildren()) {
                    $menu->submenu(
                        $this->makeDropdownTrigger($item),
                        $this->buildMenu($item->children)
                    );
                } else {
                    $menu->add(Link::to($item->url, $item->name));
                }
            }
        }

        return $menu;
    }

    protected function makeDropdownTrigger($item)
    {
        return Link::to('#', $item->name . ' <span class="caret"></span>')
            ->addClass('dropdown-toggle')
            ->setAttribute('data-toggle', 'dropdown')
            ->setAttribute('role', 'button')
            ->setAttribute('aria-haspopup', 'true')
            ->setAttribute('aria-expanded', 'false');
    }

    protected function userCanView($menuItem): bool
    {
        if (empty($menuItem->permission)) {
            return true;
        }

        return Auth::check() && Auth::user()->can($menuItem->permission);
    }
}
