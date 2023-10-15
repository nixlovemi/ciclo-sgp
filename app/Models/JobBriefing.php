<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Models\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class JobBriefing extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public $table = 'jobs_briefing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'objective',
        'background',
        'creative_details',
        'measurements',
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
    protected $casts = [];

    protected $attributes = [];

    protected $appends = [];

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
        $validation->addIdField(self::class, 'Briefing', 'id', 'ID');
        $validation->addIdField(Job::class, 'Job', 'job_id', 'Job', ['required']);
        $validation->addField('objective', ['nullable', 'string', 'min:3'], 'Objetivo');
        $validation->addField('background', ['nullable', 'string', 'min:3'], 'Histórico');
        $validation->addField('creative_details', ['nullable', 'string', 'min:3'], 'Premissas para a Criação');
        $validation->addField('measurements', ['nullable', 'string', 'min:3'], 'Medidas');
        $validation->addField('notes', ['nullable', 'string', 'min:3'], 'Observações');

        return $validation->validate();
    }
    // ===============

    // static functions
    // ================
}