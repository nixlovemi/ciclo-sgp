<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\LocalLogger;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use App\Models\Client;
use App\Models\JobBriefing;
use App\Models\JobFile;
use App\Models\JobInvoice;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Job extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const STATUS_JOB = 'JOB';
    public const STATUS_REVIEW = 'REVIEW';
    public const STATUS_BILLING = 'BILLING';
    public const STATUS_DONE = 'DONE';
    public const STATUS_CANCEL = 'CANCEL';

    public const JOB_STATUSES = [
        self::STATUS_JOB => 'Em Andamento',
        self::STATUS_REVIEW => 'Em Revisão',
        self::STATUS_BILLING => 'Faturamento',
        self::STATUS_DONE => 'Finalizado',
        self::STATUS_CANCEL => 'Cancelado',
    ];
    public const JOB_STATUSES_ADD = [
        self::STATUS_JOB => 'Em Andamento',
        self::STATUS_REVIEW => 'Em Revisão',
        self::STATUS_BILLING => 'Faturamento',
        self::STATUS_DONE => 'Finalizado',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'title',
        'responsible',
        'user_responsible_id',
        'due_date',
        'status',
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
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];

    protected $attributes = [];

    protected $appends = [
        'statusDescription',
        'formattedDueDate',
        'isInLastWeek'
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

    public function briefing()
    {
        return $this->hasOne(
            JobBriefing::class, 'job_id',
            'id'
        );
    }

    public function files()
    {
        return $this->hasMany(
            JobFile::class, 'job_id',
            'id'
        );
    }

    public function quote()
    {
        return $this->hasOne(
            Quote::class, 'id',
            'quote_id'
        );
    }

    public function invoice()
    {
        return $this->hasOne(
            JobInvoice::class, 'job_id',
            'id'
        );
    }

    public function userResponsible()
    {
        return $this->hasOne(
            User::class, 'id',
            'user_responsible_id'
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
        $validation->addIdField(self::class, 'Job', 'id', 'ID');
        $validation->addIdField(Client::class, 'Cliente', 'client_id', 'Cliente', ['required']);
        $validation->addField('title', ['required', 'string', 'min:3', 'max:60'], 'Título');
        $validation->addField('responsible', ['nullable', 'string', 'min:3', 'max:60'], 'Responsável');
        $validation->addIdField(User::class, 'Responsável Ciclo', 'user_responsible_id', 'Responsável Ciclo', []);
        $validation->addField('due_date', ['required', 'date', 'date_format:Y-m-d'], 'Prev. Entrega');
        $validation->addField('status', ['required', function ($attribute, $value, $fail) {
            if (!in_array($value, array_keys(Job::JOB_STATUSES))) {
                $fail('O status do Job não é válido');
            }
        }], 'Status');

        return $validation->validate();
    }

    public function getStatusDescriptionAttribute(): ?string
    {
        return self::JOB_STATUSES[$this->status] ?? '';
    }

    public function unlinkQuote(): ApiResponse
    {
        try {
            $quoteId = $this->quote_id;
            $this->quote_id = null;
            $this->update();
            $this->refresh();

            return new ApiResponse(false, 'Orçamento desvinculado com sucesso!', [
                'Job' => $this,
                'quoteId' => $quoteId,
            ]);
        } catch (\Throwable $th) {
            LocalLogger::log('Erro ao desvincular orçamento! Msg: ' . $th->getMessage());
            return new ApiResponse(true, 'Erro ao desvincular orçamento!');
        }
    }

    public function getFormattedDueDateAttribute(): string
    {
        return SysUtils::timezoneDate($this->due_date, 'd/m/Y');
    }

    public function getIsInLastWeekAttribute(): bool
    {
        $dueDate = \Carbon\Carbon::parse($this->due_date);
        $now = \Carbon\Carbon::now();
        $days = $dueDate->diffInDays($now);

        return ($days <= 7);
    }
    // ===============

    // static functions
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = self::getNextUid($model);
            if (!$model->create_user_id) {
                $model->create_user_id = SysUtils::getLoggedInUser()?->id;
            }
        });

        static::updating(function ($model) { });
    }

    private static function getNextUid(Job $Job): string
    {
        // lock
        DB::unprepared('LOCK TABLES '. $Job->getTable() .' WRITE');

        // UID
        $uid = 0;
        $JobLast = Job::orderBy('id', 'DESC')->first();
        if ($JobLast) {
            $uid = filter_var($JobLast->uid ?? '0', FILTER_SANITIZE_NUMBER_INT);
        }

        // unlock
        DB::unprepared('UNLOCK TABLES');

        return 'PIT ' . ($uid + 1);
    }

    public static function getShowJobsData(): array
    {
        return Job::whereIn('status', [self::STATUS_JOB, self::STATUS_REVIEW])
            ->orderBy('due_date', 'DESC')
            ->get()
            ->toArray();
    }

    public static function orderShowJobs(array $arrJobs): array
    {
        // order by isInLastWeek DESC
        usort($arrJobs, function($a, $b) {
            return strcmp($b['isInLastWeek'], $a['isInLastWeek']);
        });
        return $arrJobs;
    }

    public static function showJobsCard(array $job): string
    {
        // vars
        $dueDate = $job['formattedDueDate'] ?? '';
        $uidPit = $job['uid'] ?? '';
        $title = $job['title'] ?? '';
        $responsible = $job['responsible_user']['name'] ?? '';
        $isInLastWeek = $job['isInLastWeek'];

        // class when 7 days or less
        $class = ($isInLastWeek) ? 'last-week': '';

        return <<<HTML
            <div class="card $class">
                <div class="card-body">
                    <p class="card-text">
                        <div class="row">
                            <div class="col-2">
                                $dueDate
                            </div>

                            <div class="col-2">
                                $uidPit
                            </div>

                            <div class="col-6">
                                $title
                            </div>

                            <div class="col-2" style="font-size:80%;">
                                $responsible
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        HTML;
    }
    // ================
}