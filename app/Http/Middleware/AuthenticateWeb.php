<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\SysUtils;
use App\View\Components\Notification;
use Illuminate\Support\Facades\Route;
use App\Helpers\Permissions;

class AuthenticateWeb
{
    public function handle($request, Closure $next)
    {
        $User = SysUtils::getLoggedInUser();
        if (null === $User) {
            return $this->redirectNoPermission();
        }

        $routeName = Route::currentRouteName();
        $canAccess = Permissions::canViewOrEdit($User, $routeName);
        if (false === $canAccess) {
            return $this->redirectNoPermission();
        }

        // all good
        return $next($request);
    }

    private function redirectNoPermission()
    {
        Notification::setWarning('Atenção!', 'Você não tem acesso a esse conteúdo! Faça o login novamente.');
        return redirect()->route('site.login');
    }
}