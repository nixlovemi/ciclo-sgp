<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    public function fCheckPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
    // ===============

    // static functions
    public static function fPasswordHash(string $password): string
    {
        // return bcrypt($password);
        return Hash::make($password);
    }
    // ================
}
