<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Models\QuoteItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ServiceItem extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const CURRENCY_TYPES = [
        'R$',
        '$',
    ];
    public const PRICE_THOUSAND_SEP = '.';
    public const PRICE_DECIMAL_SEP = ',';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'currency',
        'price',
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
    protected $casts = [];

    protected $attributes = [];

    protected $appends = [
        'currencyPrice',
        'formattedPrice'
    ];

    // relations
    public function quoteItems()
    {
        return $this->hasMany(
            QuoteItem::class, 'item_id',
            'id'
        );
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, 'Item', 'id', 'ID');
        $validation->addField('description', ['required', 'string', 'min:3', 'max:80'], 'Descrição');
        $validation->addField('currency', ['required', function ($attribute, $value, $fail) {

            if (false === array_search($value, ServiceItem::CURRENCY_TYPES)) {
                $fail('O moeda do item não é válida');
            }
            
        }], 'Moeda');
        $validation->addField('price', ['required', 'numeric', 'gt:0'], 'Preço');

        return $validation->validate();
    }

    public function getCurrencyPriceAttribute(): ?string
    {
        return $this->currency . ' ' . $this->formattedPrice;
    }

    public function getFormattedPriceAttribute(): ?string
    {
        return number_format($this->price, 2, self::PRICE_DECIMAL_SEP, self::PRICE_THOUSAND_SEP);
    }
    // ===============

    // static functions
    // ================
}
