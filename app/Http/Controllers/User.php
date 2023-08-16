<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User as mUser;
use App\Helpers\SysUtils;
use App\Helpers\ApiResponse;

class User extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const ADD_USER_DATA = 'add_user_data';

    public function index()
    {
        return view('user.index');
    }

    public function view(string $codedId)
    {
        /** @var ?mUser $User */
        $User = mUser::getModelByCodedId($codedId);

        return view('user.register', [
            'title' => 'Visualizar',
            'type' => 'view',
            'action' => '',
            'User' => $User,
        ]);
    }

    public function add()
    {
        $User = new mUser();
        $User->fill(
            session()->pull(self::ADD_USER_DATA, [])
        );

        return view('user.register', [
            'title' => 'Adicionar',
            'type' => 'add',
            'action' => route('user.add'),
            'User' => $User,
        ]);
    }

    public function addSave(Request $request)
    {
        $fields = $this->getUserFormFields($request);

        $User = new mUser();
        $User->makeVisible(['password'])->fill($fields);
        $validate = $User->validateModel();
        if ($validate->isError()) {
            session([
                self::ADD_USER_DATA => $fields
            ]);

            return $this->setNotificationRedirect(
                new ApiResponse(true, $this->getValidateMessage($validate)),
                'user.add'
            );
        }
        
        $User->password = mUser::fPasswordHash($User->password);
        $User->save();
        $User->refresh();

        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Usuário inserido com sucesso!'),
            'user.edit',
            ['codedId' => $User->codedId]
        );
    }

    public function edit(string $codedId)
    {
        /** @var ?mUser $User */
        $User = mUser::getModelByCodedId($codedId);

        return view('user.register', [
            'title' => 'Editar',
            'type' => 'edit',
            'action' => route('user.edit', ['codedId' => $User?->codedId]),
            'User' => $User,
        ]);
    }

    public function editSave(Request $request, string $codedId)
    {
        $fields = $this->getUserFormFields($request);
        unset($fields['password']); // we just set this on create or user menu to change password

        /** @var mUser $User */
        $User = mUser::getModelByCodedId($codedId);
        if (!$User) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, 'Erro ao buscar usuário para edição!'),
                'user.edit',
                ['codedId' => $codedId]
            );
        }

        $User->fill($fields);
        $validate = $User->makeVisible(['password'])->validateModel();
        if ($validate->isError()) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, $this->getValidateMessage($validate)),
                'user.edit',
                ['codedId' => $codedId]
            );
        }
        
        $User->update();
        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Usuário editado com sucesso!'),
            'user.edit',
            ['codedId' => $codedId]
        );
    }

    private function getUserFormFields(Request $request): array
    {
        return [
            'name' => $request->input('user-name') ?: null,
            'email' => $request->input('user-email') ?: null,
            'password' => $request->input('user-password') ?: null,
            'role' => $request->input('user-role') ?: null,
        ];
    }

    public function changePwd()
    {
        return view('user.changePwd', []);
    }

    public function doChangePwd(Request $request)
    {
        $fields = [
            'currentPwd' => $request->input('current_pwd') ?: '',
            'newPwd' => $request->input('new_pwd') ?: '',
            'newPwdRetype' => $request->input('new_pwd_retype') ?: '',
        ];
        
        $User = SysUtils::getLoggedInUser();
        if (null === $User) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, 'Usuário não encontrado!'),
                'user.changePwd'
            );
        }

        $changeRet = $User->changePassword(
            $fields['newPwd'],
            $fields['newPwdRetype'],
            $fields['currentPwd']
        );
        return $this->setNotificationRedirect(
            $changeRet,
            'user.changePwd'
        );
    }

    public function resetPwd(string $codedId)
    {
        /** @var ?mUser $User */
        $User = mUser::getModelByCodedId($codedId);

        return view('user.forcePwd', [
            'User' => $User
        ]);
    }

    public function doResetPwd(Request $request)
    {
        $fields = [
            'codedId' => $request->input('uid') ?: '',
            'newPwd' => $request->input('new_pwd') ?: '',
            'newPwdRetype' => $request->input('new_pwd_retype') ?: '',
        ];

        /** @var mUser $User */
        $User = mUser::getModelByCodedId($fields['codedId']);
        if (!$User) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, 'Erro ao buscar usuário para resetar senha!'),
                'user.resetPwd',
                ['codedId' => $fields['codedId']]
            );
        }

        $changeRet = $User->changePassword(
            $fields['newPwd'],
            $fields['newPwdRetype']
        );
        return $this->setNotificationRedirect(
            $changeRet,
            'user.resetPwd',
            ['codedId' => $fields['codedId']]
        );
    }

    public function profile()
    {
        return view('user.profile', []);
    }

    public function saveProfile(Request $request)
    {
        $fields = [
            'name' => $request->input('user-name') ?: null,
            'email' => $request->input('user-email') ?: null,
        ];

        $User = SysUtils::getLoggedInUser();
        if (!$User) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, 'Erro ao buscar usuário para editar perfil!'),
                'user.profile'
            );
        }

        $User->fill($fields);
        $validate = $User->makeVisible(['password'])->validateModel();
        if ($validate->isError()) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, $this->getValidateMessage($validate)),
                'user.profile'
            );
        }
        
        $User->update();
        $file = $request->file('user-picture');
        if ($file) {
            $User->setNewProfilePicture($file);
        }

        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Usuário editado com sucesso!'),
            'user.profile'
        );
    }
}