<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Spatie\Menu\Laravel\Menu as SpatieMenu;
use Spatie\Menu\Link;

class MenuBuilder
{
    /**
     * Build menu for a given area (web|filament). Default: web
     */
    public function build($area = 'web')
    {
        $menuItems = Menu::with('children')
            ->whereNull('parent_id')
            ->where('area', $area)
            ->orderBy('order')
            ->get();

        return $this->buildMenu($menuItems, $area);
    }

    protected function buildMenu($items, $area = 'web')
    {
        // Render horizontal top-level menu with cascading vertical children
        $menu = SpatieMenu::new()
            ->addClass('flex items-center space-x-4')
            ->setActiveFromRequest();

        // Tailwind classes for links
        // Use brand colors: primary for text, gold for CTAs
        $linkClass = 'text-primary hover:text-white hover:bg-primary-600 px-3 py-2 rounded-md text-sm font-medium transition';
        $parentClass = 'text-primary font-semibold px-3 py-2 text-sm';

        foreach ($items as $item) {
            if ($this->userCanView($item)) {
                if ($item->hasChildren()) {
                    // build submenu as vertical list
                    // Submenu vertical list (will render below/after the parent element)
                    $sub = SpatieMenu::new()->addClass('flex flex-col mt-1 ml-4 space-y-1 bg-white dark:bg-gray-800 rounded-md shadow-sm p-1');

                    foreach ($item->children->where('area', $area) as $child) {
                        if ($this->userCanView($child)) {
                            // Standard submenu link styling (no automatic CTA detection)
                            $childClass = 'block px-4 py-2 text-sm text-primary hover:bg-primary-600 hover:text-white transition';
                            $sub->add(Link::to($child->url, $child->name)->addClass($childClass));
                        }
                    }

                    $menu->submenu(Link::to($item->url ?: '#', $item->name)->addClass($parentClass), $sub);
                } else {
                    $menu->add(Link::to($item->url, $item->name)->addClass($linkClass));
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
