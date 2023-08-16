<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\SysUtils;
use App\Models\User;

class Login extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        SysUtils::logout(false);
        return view('login');
    }

    public function doLogin(Request $request)
    {
        $form = $request->only(['email', 'pwd']);
        $response = User::fLogin($form['email'], $form['pwd']);
        if ($response->isError()) {
            return $this->setNotificationRedirect(
                $response,
                'site.login'
            );
        }

        return redirect()->route('site.dashboard');
    }

    public function recoverPassword()
    {
        SysUtils::logout(false);
        return view('loginRecoverPwd');
    }

    public function doRecoverPassword(Request $request)
    {
        $form = $request->only(['email']);
        $response = User::fRecoverPwd($form['email']);

        return $this->setNotificationRedirect(
            $response,
            'site.recoverPwd'
        );
    }

    public function changeNewPwd($idKey)
    {
        SysUtils::logout(false);
        return view('loginChangeNewPwd', [
            'ID_KEY' => $idKey
        ]);
    }

    public function doChangeNewPwd(Request $request)
    {
        $idKey = $request->input('ik') ?: '';
        $newPassword = $request->input('new_pwd') ?: '';
        $newPasswordCheck = $request->input('new_pwd_retype') ?: '';

        $response = User::fResetPasswordByToken(
            $idKey,
            $newPassword,
            $newPasswordCheck,
        );
        if ($response->isError()) {
            return $this->setNotificationRedirect(
                $response,
                'site.changeNewPwd',
                ['idKey' => $idKey]
            );
        }

        // all good
        return $this->setNotificationRedirect(
            $response,
            'site.login'
        );
    }
}
