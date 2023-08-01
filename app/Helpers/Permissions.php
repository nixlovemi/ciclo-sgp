<?php

namespace App\Helpers;

use App\Models\User;

final class Permissions {
    private const OPTION_VIEW = 'view';
    private const OPTION_EDIT = 'edit';

    private const PERMISSIONS = [

        User::ROLE_MANAGER => [
            'site.dashboard' => [Permissions::OPTION_VIEW => true, Permissions::OPTION_EDIT => true],
            'client.index' => [Permissions::OPTION_VIEW => true, Permissions::OPTION_EDIT => true],
        ],

        User::ROLE_CREATIVE => [
            'site.dashboard' => [Permissions::OPTION_VIEW => true, Permissions::OPTION_EDIT => true],
            'client.index' => [Permissions::OPTION_VIEW => false, Permissions::OPTION_EDIT => false],
        ],

        User::ROLE_CUSTOMER => [
            'site.dashboard' => [Permissions::OPTION_VIEW => true, Permissions::OPTION_EDIT => true],
            'client.index' => [Permissions::OPTION_VIEW => true, Permissions::OPTION_EDIT => false],
        ],

    ];

    public static function canView(User $User, string $routeName): bool
    {
        return self::checkPermission($User, $routeName, self::OPTION_VIEW);
    }

    public static function canEdit(User $User, string $routeName): bool
    {
        return self::checkPermission($User, $routeName, self::OPTION_EDIT);
    }

    public static function canViewOrEdit(User $User, string $routeName): bool
    {
        if (self::canView($User, $routeName)) {
            return true;
        }

        if (self::canEdit($User, $routeName)) {
            return true;
        }

        return false;
    }

    private static function checkPermission(User $User, string $routeName, string $option): bool
    {
        if ($User->isAdmin()) {
            return true;
        }

        $role = $User->role ?? '';
        $permRole = self::PERMISSIONS[$role] ?? false;
        if (false === $permRole) {
            return false;
        }

        $permRoleRoute = $permRole[$routeName] ?? false;
        if (false === $permRoleRoute) {
            return false;
        }

        // final result
        $permRoleRouteOption = $permRoleRoute[$option] ?? false;

        // check for true = all blocked except when true
        return (true === $permRoleRouteOption);
    }
}