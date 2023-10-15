<?php

namespace App\View\Components;

use App\Helpers\Permissions;
use App\Helpers\SysUtils;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class MainMenu extends Component
{
    public const KEY_ROUTE_NAME = 'routeName';
    public const KEY_ICON = 'icon';
    public const KEY_LABEL = 'label';
    public const KEY_SUBITEMS = 'subItems'; // one level
    public const KEY_DIVIDER = 'divider';
    public const KEY_ROUTE_ACL = 'routeAcl';
    public const JS_URL = 'javascript:;';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public array $menuItems = []
    ) {
        if (count($this->menuItems) === 0) {
            $this->menuItems = SysUtils::getMainMenuItems();
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if (count($this->menuItems) === 0) {
            return;
        }

        return view('components.main-menu');
    }

    public static function isDivider(array $menu): bool
    {
        return isset($menu[self::KEY_DIVIDER]) && true === $menu[self::KEY_DIVIDER];
    }

    public static function hasSubItems(array $menu): bool
    {
        return array_key_exists(self::KEY_SUBITEMS, $menu) && !empty($menu[self::KEY_SUBITEMS]);
    }

    public static function getRouteUrl(array $menu): string
    {
        if (
            !array_key_exists(self::KEY_ROUTE_NAME, $menu) ||
            !Route::has($menu[self::KEY_ROUTE_NAME])
        ) {
            return self::JS_URL;
        }

        return route($menu[self::KEY_ROUTE_NAME]);
    }

    public static function getSubItems(array $menu): array
    {
        return $menu[self::KEY_SUBITEMS] ?? [];
    }

    public static function getSpanClasses(array $menu): string
    {
        $classes = ['hide-menu'];
        $menuRoute = self::getRouteUrl($menu);

        if (self::JS_URL === $menuRoute && !array_key_exists(self::KEY_SUBITEMS, $menu)) {
            // no route and no subitems, then add this class
            $classes[] = 'text-decoration-line-through';
        }
        return implode(' ', $classes);
    }

    public static function checkPermission(array $menu): bool
    {
        // divider, all good
        if (self::isDivider($menu)) {
            return true;
        }

        if (array_key_exists(self::KEY_ROUTE_NAME, $menu)) {
            return Permissions::checkPermission($menu[self::KEY_ROUTE_NAME]);
        }

        if (array_key_exists(self::KEY_ROUTE_ACL, $menu)) {
            return Permissions::checkPermission($menu[self::KEY_ROUTE_ACL]);
        }

        return false;
    }

    public static function getLiHtml(array $menu): string
    {
        $menuRoute = self::getRouteUrl($menu);
        $currentRoute = url()->full();

        // Determine LI class
        $liClass = ['sidebar-item'];
        if (strpos($currentRoute, $menuRoute) !== false) {
            $liClass[] = 'selected';
        }
        $strLiClass = implode(' ', $liClass);

        // Determine LI > A class
        $liAClass = ['sidebar-link'];
        $htmlSubItem = '';

        if (self::hasSubItems($menu)) {
            $liAClass[] = 'has-arrow';
            $htmlSubItem .= '<ul aria-expanded="false" class="collapse first-level base-level-line">';

            foreach (self::getSubItems($menu) as $subMenu) {
                $href = self::getRouteUrl($subMenu);
                $label = $subMenu[self::KEY_LABEL] ?? 'KEY_LABEL';
                $spanClasses = self::getSpanClasses($subMenu);

                $htmlSubItem .= '<li class="sidebar-item">';
                $htmlSubItem .= '  <a href="' . $href . '" class="sidebar-link">';
                $htmlSubItem .= '    <span class="hide-menu '.$spanClasses.'">' . $label . '</span>';
                $htmlSubItem .= '  </a>';
                $htmlSubItem .= '</li>';
            }

            $htmlSubItem .= '</ul>';
        }
        $strLiAClass = implode(' ', $liAClass);

        // Determine LI > A > SPAN class
        $strLiASpanClass = self::getSpanClasses($menu);
        $iconHtml = $menu[self::KEY_ICON] ?? '';
        $label = $menu[self::KEY_LABEL] ?? 'KEY_LABEL';

        // Generate the HTML output
        return <<<HTML
            <li class="$strLiClass">
                <a class="$strLiAClass" href="$menuRoute" aria-expanded="false">
                    $iconHtml
                    <span class="$strLiASpanClass">
                        $label
                    </span>
                </a>
                $htmlSubItem
            </li>
        HTML;
    }
}
