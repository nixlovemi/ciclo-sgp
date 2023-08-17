@inject('MainMenu', 'App\View\Components\MainMenu')
@inject('Route', 'Illuminate\Support\Facades\Route')

<nav class="sidebar-nav">
    <ul id="sidebarnav">
        @foreach ($menuItems as $menu)
            @if ($MainMenu::isDivider($menu))

                <li class="list-divider"></li>

            @elseif ($MainMenu::checkPermission($menu))
            
                {!! $MainMenu::getLiHtml($menu) !!}

            @endif
        @endforeach
    </ul>
</nav>