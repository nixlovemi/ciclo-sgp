<?php

namespace App\Helpers;

use App\Models\User;
use App\Helpers\SysUtils;

final class Permissions {
    public const ACL_DASHBOARD_VIEW = 'dashboard/view';
    public const ACL_CLIENT_VIEW = 'client/view';
    public const ACL_CLIENT_EDIT = 'client/edit';
    public const ACL_USER_VIEW = 'user/view';
    public const ACL_USER_EDIT = 'user/edit';
    public const ACL_USER_CHANGE_PWD = 'user/changePwd';

    private const ACL = [
        self::ACL_DASHBOARD_VIEW => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_CUSTOMER],

        self::ACL_CLIENT_VIEW => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
        self::ACL_CLIENT_EDIT => [User::ROLE_MANAGER],

        self::ACL_USER_CHANGE_PWD => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_CUSTOMER],
        self::ACL_USER_VIEW => [User::ROLE_MANAGER],
        self::ACL_USER_EDIT => [],
    ];

    private const ROUTE_ACL = [
        'site.dashboard' => self::ACL_DASHBOARD_VIEW,

        'client.index' => self::ACL_CLIENT_VIEW,
        'client.view' => self::ACL_CLIENT_VIEW,
        'client.add' => self::ACL_CLIENT_EDIT,
        'client.add.save' => self::ACL_CLIENT_EDIT,
        'client.edit' => self::ACL_CLIENT_EDIT,
        'client.edit.save' => self::ACL_CLIENT_EDIT,

        'user.index' => self::ACL_USER_VIEW,
        'user.view' => self::ACL_USER_VIEW,
        'user.add' => self::ACL_USER_EDIT,
        'user.add.save' => self::ACL_USER_EDIT,
        'user.edit' => self::ACL_USER_EDIT,
        'user.edit.save' => self::ACL_USER_EDIT,
        'user.changePwd' => self::ACL_USER_CHANGE_PWD,
        'user.doChangePwd' => self::ACL_USER_CHANGE_PWD,
        'user.resetPwd' => self::ACL_USER_EDIT,
        'user.doResetPwd' => self::ACL_USER_EDIT,
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