<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User as mUser;
use App\View\Components\Notification;

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
            Notification::setWarning('Atenção!', $this->getValidateMessage($validate));
            session([
                self::ADD_USER_DATA => $fields
            ]);
            return redirect()->route('user.add');
        }
        
        $User->password = mUser::fPasswordHash($User->password);
        $User->save();
        $User->refresh();
        Notification::setSuccess('Sucesso!', 'Usuário inserido com sucesso!');
        return redirect()->route('user.edit', ['codedId' => $User->codedId]);
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
            Notification::setWarning('Atenção!', 'Erro ao buscar usuário para edição!');
            return redirect()->route('user.edit', ['codedId' => $codedId]);
        }

        $User->fill($fields);
        $validate = $User->makeVisible(['password'])->validateModel();
        if ($validate->isError()) {
            Notification::setWarning('Atenção!', $this->getValidateMessage($validate));
            return redirect()->route('user.edit', ['codedId' => $codedId]);
        }
        
        $User->update();
        Notification::setSuccess('Sucesso!', 'Usuário editado com sucesso!');
        return redirect()->route('user.edit', ['codedId' => $codedId]);
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
}