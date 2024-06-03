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

class JobFile extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public $table = 'jobs_file';

    private const FILES_FOLDER = '/public/jobFile';

    public const TYPE_FILE = 'FILE';
    public const TYPE_URL = 'URL';
    public const JOB_FILE_TYPES = [
        self::TYPE_FILE => 'Arquivo',
        self::TYPE_URL => 'Link',
    ];

    public const JOB_SECTION_BRIEFING_FINAL_REVIEW = 'BRIEFING_FINAL_REVIEW';
    public const JOB_SECTION_BRIEFING_FINALIZATION = 'BRIEFING_FINALIZATION';
    public const JOB_SECTIONS = [
        self::JOB_SECTION_BRIEFING_FINAL_REVIEW => 'Revisão Final',
        self::JOB_SECTION_BRIEFING_FINALIZATION => 'Finalização',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'title',
        'url',
        'type',
        'job_section',
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
        'typeDescription',
        'formattedCreatedAtDate',
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
        $validation->addField('title', ['required', 'string', 'min:3', 'max:60'], 'Título');
        $validation->addField('url', ['required', 'string', 'min:3'], 'URL');
        $validation->addField('type', ['required', function ($attribute, $value, $fail) {
            if (!in_array($value, array_keys(self::JOB_FILE_TYPES))) {
                $fail('O tipo do arquivo não é válido');
            }
        }], 'Tipo');
        $validation->addField('job_section', [function ($attribute, $value, $fail) {
            if (!empty($value) && !in_array($value, array_keys(self::JOB_SECTIONS))) {
                $fail('O sub-tipo do arquivo não é válido');
            }
        }], 'Sub-Tipo');

        return $validation->validate();
    }

    public function getTypeDescriptionAttribute(): ?string
    {
        return self::JOB_FILE_TYPES[$this->type] ?? '';
    }

    public function getUrlAttribute($url): ?string
    {
        if (Storage::exists($url)) {
            $storageUrl = str_replace('/public/', '/public/storage/', $url);
            return $storageUrl;
        }

        return $url;
    }

    public function getFormattedCreatedAtDateAttribute(): string
    {
        return SysUtils::timezoneDate($this->created_at, 'd/m/Y');
    }

    public function addFile(UploadedFile $file): ApiResponse
    {
        $retValidate = $this->validateFile($file);
        if ($retValidate->isError()) {
            return $retValidate;
        }

        $newFileName = date('YmdHis') . '-' . $file->getClientOriginalName();
        $fullPath = self::FILES_FOLDER . '/' . $newFileName;
        $retPut = Storage::disk('local')->put($fullPath, file_get_contents($file));
        if (false === $retPut) {
            return new ApiResponse(true, 'Erro ao fazer upload do arquivo!');
        }

        // all good
        $this->url = $fullPath;
        return new ApiResponse(false, 'Upload efetuado com sucesso!', [
            'JobFile' => $this,
        ]);
    }

    private function validateFile(UploadedFile $file): ApiResponse
    {
        // 10MB
        if ($file->getSize() > 10000000) {
            return new ApiResponse(true, 'Arquivo excede limite de 10MB!');
        }

        // all good
        return new ApiResponse(false, 'Arquivo validado!');
    }
    // ===============

    // static functions
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->create_user_id) {
                $model->create_user_id = SysUtils::getLoggedInUser()?->id;
            }
        });

        static::deleted(function ($model) {
            if (self::TYPE_FILE === $model->type) {
                self::deleteModelUrlFile($model);
            }
        });
    }

    public static function deleteModelUrlFile(JobFile $model): void
    {
        // try to delete on the public folder
        $fullFilePath = public_path($model->url);
        if (file_exists($fullFilePath)) {
            if (false === unlink($fullFilePath)) {
                \App\Helpers\LocalLogger::log('Erro ao deletar arquivo: ' . $model->url . '. ID: ' . $model->id);
            }

            return;
        }

        // try to delete on storage
        if (Storage::exists($model->url)) {
            if (false === Storage::delete($model->url)) {
                \App\Helpers\LocalLogger::log('Erro ao deletar arquivo: ' . $model->url . '. ID: ' . $model->id);
            }

            return;
        }
    }
    // ================
}