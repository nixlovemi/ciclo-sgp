<?php

namespace App\Tables;

use App\Models\Job;
use App\Models\JobFile;
use App\Helpers\Permissions;
use App\Helpers\SysUtils;
use App\Models\User;
use App\Tables\HeadActions\OpenModalHeadAction;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\RowActions\DestroyRowAction;
use Okipa\LaravelTable\RowActions\RedirectRowAction;

class JobsFileTable extends AbstractTableConfiguration
{
    public int $vJobId;
    protected Job $Job;
    public bool $vDisabled;
    public array $vJobSections = [];
    protected ?User $User;

    protected function table(): Table
    {
        // $hasJobEdit = Permissions::checkPermission(Permissions::ACL_JOB_EDIT);
        // $hasJobView = Permissions::checkPermission(Permissions::ACL_JOB_VIEW);
        $this->varSetUp();

        return Table::make()
            ->model(JobFile::class)
            ->query(function(Builder $query) {
                $query->where('job_id', '=', $this->vJobId ?? 0);

                // filter by vJobSections
                if (empty($this->vJobSections)) {
                    $query->where('job_section', '=', null);
                } else {
                    $query->whereIn('job_section', $this->vJobSections);
                }

                return $query->orderBy('id', 'ASC');
            })
            ->numberOfRowsPerPageOptions([10])
            ->rowActions(fn(JobFile $JobFile) => [
                (new RedirectRowAction($JobFile->url, 'Abrir', '<i class="fas fa-folder-open"></i>', ['link-info'], null, null, true)),
                (new DestroyRowAction())
                    ->confirmationQuestion(__('Confirma a exclusão do arquivo ":file"?', [
                        'file' => $JobFile->title,
                    ]))
                    ->feedbackMessage(false)
                    ->when($this->rowActionDeleteWhen()),
            ])
            ->headAction(
                (new OpenModalHeadAction(route('jobFile.add', ['jobCodedId' => $this->Job->codedId, 'json' => true]), 'Adicionar', '<i class="fas fa-plus"></i>', []))
                    ->when($this->headActionAddWhen())
            );
    }

    protected function columns(): array
    {
        return [
            Column::make('title')->title('Título')->searchable(),
            Column::make('type')
                ->title('Tipo')
                ->searchable(function($query, string $searchBy) {
                    $type = array_search($searchBy, JobFile::JOB_FILE_TYPES);
                    if (false === $type) {
                        return;
                    }

                    return $query->where('type', '=', $type);
                })
                ->format(function(JobFile $JobFile) {
                    return $JobFile->typeDescription;
                }),
        ];
    }

    protected function rowActionDeleteWhen(): bool
    {
        return Permissions::checkPermission(Permissions::ACL_JOB_EDIT) &&
            !$this->vDisabled &&
            ($this->User->isAdmin() || $this->User->isManager() || $this->User->isCustomer());
    }

    protected function headActionAddWhen(): bool
    {
        // same, for now
        return $this->rowActionDeleteWhen();
    }

    private function varSetUp(): void
    {
        $this->Job = Job::find($this->vJobId);
        $this->User = SysUtils::getLoggedInUser();
    }
}
