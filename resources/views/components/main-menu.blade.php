@inject('MainMenu', 'App\View\Components\MainMenu')
@inject('Route', 'Illuminate\Support\Facades\Route')
@inject('Permissions', 'App\Helpers\Permissions')

<nav class="sidebar-nav">
    <ul id="sidebarnav">
        @foreach ($menuItems as $menu)
            @if (isset($menu[$MainMenu::KEY_DIVIDER]) && true === $menu[$MainMenu::KEY_DIVIDER])
                <li class="list-divider"></li>
            @elseif ($Permissions::canViewOrEdit($loggedUser, $menu[$MainMenu::KEY_ROUTE_NAME]))
                <li class="sidebar-item">
                    <a
                        class="sidebar-link sidebar-link"
                        href="{{ ($Route::has($menu[$MainMenu::KEY_ROUTE_NAME])) ? route($menu[$MainMenu::KEY_ROUTE_NAME]): 'javascript:;' }}"
                        aria-expanded="false"
                    >
                        {!! $menu[$MainMenu::KEY_ICON] ?? '' !!}
                        <span @class([
                            'hide-menu',
                            'text-decoration-line-through' => false === $Route::has($menu[$MainMenu::KEY_ROUTE_NAME])
                        ])>
                            {{ $menu[$MainMenu::KEY_LABEL] ?? 'KEY_LABEL' }}
                        </span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>