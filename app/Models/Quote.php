<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\LocalLogger;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use App\Models\Client;
use App\Models\Job;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Quote extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const DEFAULT_VALIDITY_DAYS = 30;
    public const DEFAULT_PAYMENT = 30;

    public const PAYMENT_IN_CASH = 'À Vista';
    public const PAYMENT_7_DAYS = '7 dias';
    public const PAYMENT_15_DAYS = '15 dias';
    public const PAYMENT_30_DAYS = '30 dias';
    public const PAYMENT_45_DAYS = '45 dias';
    public const PAYMENT_60_DAYS = '60 dias';
    public const PAYMENT_90_DAYS = '90 dias';
    public const PAYMENT_120_DAYS = '120 dias (fora o mês)';
    public const PAYMENT_TYPES = [
        self::PAYMENT_IN_CASH,
        self::PAYMENT_7_DAYS,
        self::PAYMENT_15_DAYS,
        self::PAYMENT_30_DAYS,
        self::PAYMENT_45_DAYS,
        self::PAYMENT_60_DAYS,
        self::PAYMENT_90_DAYS,
        self::PAYMENT_120_DAYS
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'create_user_id',
        'client_id',
        'validity_days',
        'payment_type',
        'payment_type_memo',
        'notes',
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

    protected $attributes = [
        'validity_days' => self::DEFAULT_VALIDITY_DAYS,
        'payment_type' => self::PAYMENT_15_DAYS,
    ];

    protected $appends = [
        'formattedDate',
        'total',
        'formattedTotal',
    ];

    // relations
    public function createUser()
    {
        return $this->hasOne(
            User::class, 'id',
            'create_user_id'
        );
    }

    public function client()
    {
        return $this->hasOne(
            Client::class, 'id',
            'client_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            QuoteItem::class, 'quote_id',
            'id'
        );
    }

    public function job()
    {
        return $this->hasOne(
            Job::class, 'quote_id',
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
        $validation->addIdField(self::class, 'Orçamento', 'id', 'ID');
        $validation->addField('date', ['required', 'date', 'date_format:Y-m-d'], 'Data');
        $validation->addIdField(User::class, 'Usuário', 'create_user_id', 'Usuário', ['required']);
        $validation->addIdField(Client::class, 'Cliente', 'client_id', 'Cliente', ['required']);
        $validation->addField('validity_days', ['required', 'integer', 'gt:0'], 'Validade');
        $validation->addField('payment_type', ['required', function ($attribute, $value, $fail) {
            if (!in_array($value, Quote::PAYMENT_TYPES)) {
                $fail('A forma de pagamento não é válida');
            }
        }], 'Forma de Pagamento');
        $validation->addField('payment_type_memo', ['nullable', 'string'], 'Notas da Forma de Pgto');
        $validation->addField('notes', ['nullable', 'string'], 'Observações');
        
        return $validation->validate();
    }

    public function getFormattedDateAttribute(): string
    {
        return SysUtils::timezoneDate($this->date, 'd/m/Y');
    }

    public function getTotalAttribute(): float
    {
        $total = 0;
        foreach ($this->items as $QuoteItem) {
            $total += $QuoteItem->total;
        }

        return $total;
    }

    public function getFormattedTotalAttribute(): string
    {
        return SysUtils::formatCurrencyBr($this->total, 2, 'R$');
    }

    public function linkJob(?Job $Job): ApiResponse
    {
        if (!$Job) {
            return new ApiResponse(true, 'Job é inválido!');
        }

        if ($Job->quote) {
            return new ApiResponse(true, 'Job já possui orçamento vinculado!');
        }

        if ($linkedJob = $this->getLinkedJob()) {
            return new ApiResponse(true, 'Orçamento já está vinculado ao Job '.$linkedJob->uid.'!');
        }

        // all good
        try {
            $Job->quote_id = $this->id;
            $Job->update();
            $Job->refresh();

            return new ApiResponse(false, 'Orçamento vinculado com sucesso!', [
                'Job' => $Job,
                'Quote' => $this
            ]);
        } catch (\Throwable $th) {
            LocalLogger::log('Erro ao salvar job! Msg: ' . $th->getMessage());
            return new ApiResponse(true, 'Erro ao vincular job!');
        }
    }

    public function getLinkedJob(): ?Job
    {
        return Job::where('quote_id', $this->id)
            ->first();
    }
    // ===============

    // static functions
    public static function fGetAllToLinkToJob(): \Illuminate\Database\Eloquent\Collection
    {
        return Quote::distinct('quotes.id')
            ->select('quotes.*')
            ->where('quotes.active', '=', 1)
            ->leftJoin('jobs', 'jobs.quote_id', '=', 'quotes.id')
            ->whereRaw('jobs.id IS NULL')
            ->orderBy('quotes.date', 'DESC')
            ->get();
    }
    // ================
}
