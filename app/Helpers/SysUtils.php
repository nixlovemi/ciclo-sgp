<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\SessionGuard;
use App\View\Components\MainMenu;
use App\Models\User;

final class SysUtils {

    private const ENCODE_FROM_CHARS = '+/=';
    private const ENCODE_TO_CHARS = '-;$';

    public static function getWebAuth(): SessionGuard
    {
        return Auth::guard('web');
    }

    public static function getLoggedInUser(): ?User
    {
        $userId = SysUtils::getWebAuth()->id() ?? 0;
        if ($userId == 0) {
            $User = auth()->user();
            $userId = $User->id ?? 0;
        }
        return User::find($userId);
    }

    public static function loginUser(User $User): bool
    {
        $Auth = SysUtils::getWebAuth();
        if (false === $Auth->loginUsingId($User->id)) {
            return false;
        }

        return true;
    }

    public static function logout(bool $flushSession=true): void
    {
        $User = SysUtils::getLoggedInUser();
        if ($User) {
            try {
                SysUtils::getWebAuth()->logout();
            } catch (\Throwable $th) { dd($th); }
        }

        if ($flushSession) {
            // flushing the session will remove CSRF Token's value
            session()->flush();
        }
    }

    public static function applyTimezone($date)
    {
        return \Carbon\Carbon::parse($date)->timezone(getenv('APP_TIME_ZONE'));
    }

    public static function timezoneDate($date, $format): string
    {
        return \Carbon\Carbon::parse($date)->setTimezone(env('APP_TIME_ZONE'))->format($format);
    }

    public static function encodeStr(string $text): string
    {
        $base64 = base64_encode($text);
        $replacedB64 = strtr($base64, self::ENCODE_FROM_CHARS, self::ENCODE_TO_CHARS);
        $rotStr = str_rot13($replacedB64);

        return $rotStr;
    }

    public static function decodeStr(string $encodedId): ?string
    {
        $unRot = str_rot13($encodedId);
        $unreplaceB64 = strtr($unRot, self::ENCODE_TO_CHARS, self::ENCODE_FROM_CHARS);
        $originalStr = base64_decode($unreplaceB64);
        $originalWithoutSpecial = preg_replace ('/[^\p{L}\p{N}]/u', '@', $originalStr);
        
        return $originalWithoutSpecial;
    }

    /**
     * 3 levels maximum
     * @param string keys [check App\View\Components\MainMenu]
     */
    public static function getMainMenuItems(?User $User = null): array
    {
        return [
            [
                MainMenu::KEY_ROUTE_NAME => 'site.dashboard',
                MainMenu::KEY_ICON => '<i data-feather="home" class="feather-icon"></i>',
                MainMenu::KEY_LABEL => 'Dashboard',
            ],
            [MainMenu::KEY_DIVIDER => true],
            [
                MainMenu::KEY_ROUTE_NAME => 'client.index',
                MainMenu::KEY_ICON => '<i class="fas fa-suitcase"></i>',
                MainMenu::KEY_LABEL => 'Clientes',
            ],
        ];
    }
}