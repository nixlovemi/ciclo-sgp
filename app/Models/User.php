<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Helpers\ApiResponse;
use App\Helpers\SysUtils;
use App\Helpers\Constants;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Helpers\ValidatePassword;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_MANAGER = 'MANAGER';
    public const ROLE_CREATIVE = 'CREATIVE';
    public const ROLE_CUSTOMER = 'CUSTOMER';

    public const USER_ROLES = [
        self::ROLE_ADMIN => 'Administrador',
        self::ROLE_MANAGER => 'Gerência',
        self::ROLE_CREATIVE => 'Criação',
        self::ROLE_CUSTOMER => 'Atendimento'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'picture_url',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_reset_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected $appends = [
        'codedId',
    ];

    // relations
    public function clients()
    {
        return $this->hasMany(
            Client::class, 'create_user_id',
            'id'
        );
    }
    // =========

    // class functions
    public function validateModel(): ApiResponse
    {
        return new ApiResponse(true, 'Implementar');
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function getPictureUrl(): string
    {
        if (empty($this->picture_url)) {
            return Constants::USER_DEFAULT_IMAGE_PATH;
        }

        return $this->picture_url;
    }

    public function generateResetPassToken(): string
    {
        $this->password_reset_token = SysUtils::encodeStr($this->id . date('YmdHisu'));
        $this->save();

        return $this->password_reset_token;
    }

    public function isAdmin(): bool
    {
        return $this->role === User::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === User::ROLE_MANAGER;
    }

    public function isCreative(): bool
    {
        return $this->role === User::ROLE_CREATIVE;
    }

    public function isCustomer(): bool
    {
        return $this->role === User::ROLE_CUSTOMER;
    }
    // ===============

    // static functions
    public static function fPasswordHash(string $password): string
    {
        // return bcrypt($password);
        return Hash::make($password);
    }

    public static function fLogin(string $email, string $password): ApiResponse
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, 'Informe um e-mail válido!');
        }

        if (empty($password)) {
            return new ApiResponse(true, 'Preencha a senha!');
        }

        $User = User::where('email', $email)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, 'Usuário não encontrado ou inativo!');
        }

        if (false === $User->checkPassword($password)) {
            return new ApiResponse(true, 'Usuário ou senha inválido(s)!');
        }

        // all good, register everything
        if (false === SysUtils::loginUser($User)) {
            return new ApiResponse(true, 'Erro ao registrar usuário! Tente novamente.');
        }

        // clean reset token
        $User->password_reset_token = null;
        $User->save();
        $User->refresh();

        return new ApiResponse(false, 'Login efetuado com sucesso!', [
            'User' => $User
        ]);
    }

    public static function fRecoverPwd(string $email): ApiResponse
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, 'Informe um e-mail válido!');
        }

        $User = User::where('email', $email)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, 'Usuário não encontrado ou inativo!');
        }

        // generate and save token
        $token = $User->generateResetPassToken();
        $User->refresh();

        // send mail
        Mail::to($User->email)
            ->send(
                new ResetPassword([
                    'EMAIL_TITLE' => 'Você acaba de pedir para alterar sua senha',
                    'TITLE' => 'Você acaba de pedir para alterar sua senha',
                    'HEADER_IMG_FULL_URL' => (env('APP_ENV') === 'local') ? 'https://i.imgur.com/SzkGU2o.png': '/img/resetPassword.png',
                    'ARR_TEXT_LINES' => [
                        'Esqueceu a sua senha?',
                        'Nós vimos que você solicitou alteração de senha da sua conta.',
                        'Caso não tenha sido você, ignore esse e-mail. Mas fique tranquilo, a sua conta está segura com a gente!'
                    ],
                    'ACTION_BUTTON_URL' => route('site.changeNewPwd', ['idKey' => $token]),
                    'ACTION_BUTTON_TEXT' => 'Escolha sua nova senha',
                ])
            );

        return new ApiResponse(false, 'Solicitação de alteração de senha concluído!', [
            'token' => $token,
            'User' => $User,
        ]);
    }

    public static function fResetPasswordByToken(
        string $token,
        string $newPassword,
        string $newPasswordRetype
    ): ApiResponse {
        $User = User::where('password_reset_token', $token)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, 'Usuário não encontrado ou inativo!');
        }

        if ($newPassword !== $newPasswordRetype) {
            return new ApiResponse(true, 'Senhas não conferem!');
        }

        $ValidadePwd = new ValidatePassword($newPassword);
        $retValidate = $ValidadePwd->validate();
        if (true === $retValidate->isError()) {
            return $retValidate;
        }

        // all good, change it
        $User->password_reset_token = null;
        $User->password = User::fPasswordHash($newPassword);
        $User->save();
        $User->refresh();

        return new ApiResponse(false, 'Senha resetada com sucesso!', [
            'User' => $User
        ]);
    }
    // ================
}
