<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class Client extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'business_name',
        'business_id',
        'business_email',
        'business_phone',
        'postal_code',
        'street',
        'street_2',
        'city',
        'province',
        'country',
        'notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

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
    public function createUser()
    {
        return $this->hasOne(
            User::class, 'id',
            'create_user_id'
        );
    }
    // =========

    // class functions
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, 'Cliente', 'id', 'ID');
        $validation->addField('name', ['required', 'string', 'min:3', 'max:255'], 'Nome');
        $validation->addEmailField('email', 'E-mail', ['nullable', 'string', 'max:255']);
        $validation->addPhoneField('phone', 'Telefone', ['nullable', 'max:35']);
        $validation->addField('business_name', ['nullable', 'string', 'min:3', 'max:255'], 'Razão Social');
        $validation->addField('business_id', ['nullable', 'string', 'min:3', 'max:50'], 'CPF/CNPJ');
        $validation->addEmailField('business_email', 'E-mail Comercial', ['nullable', 'string', 'max:255']);
        $validation->addPhoneField('business_phone', 'Telefone Comercial', ['nullable', 'max:35']);
        $validation->addField('postal_code', ['nullable', 'max:20'], 'Código Postal');
        $validation->addField('street', ['nullable', 'string', 'min:3', 'max:255'], 'Rua, Avenida ...');
        $validation->addField('street_2', ['nullable', 'string', 'min:3', 'max:255'], 'Bairro / Complemento');
        $validation->addField('city', ['nullable', 'string', 'min:3', 'max:255'], 'Cidade');
        $validation->addProvinceField('province', 'Estado', ['nullable']);
        $validation->addCountryField('country', 'País', ['nullable']);

        return $validation->validate();
    }
    // ===============

    // static functions
    // ================
}