<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use App\Models\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class JobInvoice extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const PRICE_THOUSAND_SEP = '.';
    public const PRICE_DECIMAL_SEP = ',';
    private const FILES_FOLDER = '/public/jobInvoice';

    public $table = 'jobs_invoice';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total'
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
        'formattedInvoiceDate',
        'formattedDueDate',
        'formattedTotal',
        'currencyTotal',
    ];

    // relations
    public function job()
    {
        return $this->hasOne(
            Job::class, 'id',
            'job_id'
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
        $validation->addIdField(Job::class, 'Job', 'job_id', 'Job', ['required']);
        $validation->addField('invoice_number', ['nullable', 'string', 'min:1', 'max:30'], 'Nr. da Nota');
        $validation->addField('invoice_date', ['nullable', 'date', 'date_format:Y-m-d'], 'Data Fatura');
        $validation->addField('due_date', ['nullable', 'date', 'date_format:Y-m-d'], 'Data Vcto');
        $validation->addField('total', ['nullable', 'numeric', 'gt:0'], 'Total');
        $validation->addField('invoice_path', ['nullable', 'string', 'min:3'], 'PDF Nota');

        return $validation->validate();
    }

    public function getFormattedInvoiceDateAttribute(): string
    {
        return SysUtils::timezoneDate($this->invoice_date, 'd/m/Y');
    }

    public function getFormattedDueDateAttribute(): string
    {
        return SysUtils::timezoneDate($this->due_date, 'd/m/Y');
    }

    public function getFormattedTotalAttribute(): ?string
    {
        if (empty($this->total)) {
            return null;
        }

        return number_format($this->total, 2, self::PRICE_DECIMAL_SEP, self::PRICE_THOUSAND_SEP);
    }

    public function getCurrencyTotalAttribute(): ?string
    {
        return 'R$' . ' ' . $this->formattedTotal;
    }

    public function getInvoicePathAttribute($url): ?string
    {
        if (Storage::exists($url)) {
            $storageUrl = str_replace('/public/', '/public/storage/', $url);
            return $storageUrl;
        }

        return $url;
    }

    /**
     * Invoice Path
     */
    public function addFile(UploadedFile $file): ApiResponse
    {
        $retValidate = $this->validateFile($file);
        if ($retValidate->isError()) {
            return $retValidate;
        }

        $newFileName = SysUtils::sanitizeFileNameForUpload(
            $this->id . '-' . date('YmdHis') . '-' . $file->getClientOriginalName()
        );
        $fullPath = self::FILES_FOLDER . '/' . $newFileName;
        $retPut = Storage::disk('local')->put($fullPath, file_get_contents($file));
        if (false === $retPut) {
            return new ApiResponse(true, 'Erro ao fazer upload do arquivo!');
        }

        // all good
        $this->invoice_path = $fullPath;
        return new ApiResponse(false, 'Upload efetuado com sucesso!', [
            'JobInvoice' => $this,
        ]);
    }

    private function validateFile(UploadedFile $file): ApiResponse
    {
        // 10MB
        if ($file->getSize() > 10000000) {
            return new ApiResponse(true, 'Arquivo excede limite de 10MB!');
        }

        $mimeType = $file->getMimeType();
        if (array_search($mimeType, ['application/pdf', 'text/xml']) === false) {
            return new ApiResponse(true, 'Arquivo deve ser PDF ou XML!');
        }

        // all good
        return new ApiResponse(false, 'Arquivo validado!');
    }
    // ===============

    // static functions
    // ================
}