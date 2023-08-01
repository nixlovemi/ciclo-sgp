<?php

namespace App\Helpers;

use App\Models\User;
use App\Helpers\SysUtils;

final class Permissions {
    private const ACL_CLIENT_VIEW = 'client/view';
    private const ACL_CLIENT_EDIT = 'client/edit';
    private const ACL_DASHBOARD_VIEW = 'dashboard/view';

    private const ACL = [
        self::ACL_CLIENT_VIEW => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
        self::ACL_CLIENT_EDIT => [User::ROLE_MANAGER],

        self::ACL_DASHBOARD_VIEW => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_CUSTOMER]
    ];

    private const ROUTE_ACL = [
        'site.dashboard' => self::ACL_DASHBOARD_VIEW,
        'client.index' => self::ACL_CLIENT_VIEW
    ];

    public static function checkPermission(string $aclOrRoute, ?User $User = null): bool
    {
        $User = $User ?? SysUtils::getLoggedInUser();
        if ($User?->isAdmin()) {
            return true;
        }

        // if it's a route string, try to get ACL
        if (array_key_exists($aclOrRoute, self::ROUTE_ACL)) {
            $aclOrRoute = self::ROUTE_ACL[$aclOrRoute];
        }

        // check for true = all blocked except when true
        $hasAcl = (array_search($User?->role, self::ACL[$aclOrRoute] ?? []) !== false);
        return (true === $hasAcl);
    }
}