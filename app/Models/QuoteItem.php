<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Models\Quote;
use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class QuoteItem extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const PRICE_THOUSAND_SEP = '.';
    public const PRICE_DECIMAL_SEP = ',';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote_id',
        'item_id',
        'quantity',
        'type',
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
        'formattedQuantity',
        'formattedPrice',
        'currencyPrice',
        'formattedTotal',
        'currencyTotal',
        'total',
    ];

    // relations
    public function quote()
    {
        return $this->hasOne(
            Quote::class, 'id',
            'quote_id'
        );
    }

    public function serviceItem()
    {
        return $this->hasOne(
            ServiceItem::class, 'id',
            'item_id'
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
        $validation->addIdField(self::class, 'Item do Orçamento', 'id', 'ID');
        $validation->addIdField(Quote::class, 'Orçamento', 'quote_id', 'Orçamento');
        $validation->addIdField(ServiceItem::class, 'Item', 'item_id', 'Item');
        $validation->addField('quantity', ['required', 'numeric', 'gt:0'], 'Quantidade');
        $validation->addField('type', ['required', 'string', 'min:1', 'max:10'], 'Unidade');
        $validation->addField('price', ['required', 'numeric', 'gt:0'], 'Preço');
        
        return $validation->validate();
    }

    public function getFormattedQuantityAttribute(): ?string
    {
        return number_format($this->quantity, 2, self::PRICE_DECIMAL_SEP, self::PRICE_THOUSAND_SEP);
    }

    public function getFormattedPriceAttribute(): ?string
    {
        return number_format($this->price, 2, self::PRICE_DECIMAL_SEP, self::PRICE_THOUSAND_SEP);
    }

    public function getCurrencyPriceAttribute(): ?string
    {
        return $this->serviceItem->currency . ' ' . $this->formattedPrice;
    }

    public function getFormattedTotalAttribute(): ?string
    {
        return number_format($this->total, 2, self::PRICE_DECIMAL_SEP, self::PRICE_THOUSAND_SEP);
    }

    public function getCurrencyTotalAttribute(): ?string
    {
        return $this->serviceItem->currency . ' ' . $this->formattedTotal;
    }
    
    public function getTotalAttribute(): float
    {
        return (float) $this->quantity * $this->price;
    }
    // ===============

    // static functions
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            try {
                $model->total = number_format($model->quantity * $model->price, 2, '.', '');
            } catch (\Throwable $th) { }
        });
    }
    // ================
}
