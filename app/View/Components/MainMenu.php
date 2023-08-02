<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\SysUtils;
use App\Models\User;

class MainMenu extends Component
{
    public const KEY_ROUTE_NAME = 'routeName';
    public const KEY_ICON = 'icon';
    public const KEY_LABEL = 'label';
    public const KEY_SUBITEMS = 'subItems'; # not implemented
    public const KEY_DIVIDER = 'divider';

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
}
