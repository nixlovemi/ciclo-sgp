<?php

namespace App\Tables;

use App\Models\JobFile;
use App\Tables\JobsFileTable;
use App\Tables\HeadActions\OpenModalHeadAction;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Table;

class JobsFileTableBriefingFinalReview extends JobsFileTable
{
    protected function table(): Table
    {
        $table = parent::table();

        // adding the codedJobSection to the route
        $table->headAction(
            (new OpenModalHeadAction(
                route('jobFile.add', [
                    'jobCodedId' => $this->Job->codedId,
                    'json' => true,
                    'codedJobSection' => JobFile::JOB_SECTION_BRIEFING_FINAL_REVIEW
                ]),
                'Adicionar',
                '<i class="fas fa-plus"></i>',
                []
            ))->when($this->headActionAddWhen())
        );

        return $table;
    }

    protected function columns(): array
    {
        $columns = parent::columns();

        // add column created_at
        $columns[] = Column::make('created_at')
            ->title('Data')
            ->format(function(JobFile $JobFile) {
                return $JobFile->formattedCreatedAtDate;
            });
            
        return $columns;
    }

    protected function headActionAddWhen(): bool
    {
        return !in_array($this->Job?->status, [$this->Job::STATUS_DONE, $this->Job::STATUS_CANCEL]) &&
            ($this->User?->isAdmin() || $this->User?->isManager() || $this->User?->isEditor());
    }

    protected function rowActionDeleteWhen(): bool
    {
        // editor can't delete files
        return !in_array($this->Job?->status, [$this->Job::STATUS_DONE, $this->Job::STATUS_CANCEL]) &&
            ($this->User?->isAdmin() || $this->User?->isManager());
    }
}