<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\View\Components\Notification;
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
            Notification::setWarning('Atenção!', $response->getMessage());
            return redirect()->route('site.login');
        }

        // VALIDAR!
        return redirect()->route('site.dashboard');
    }
}
