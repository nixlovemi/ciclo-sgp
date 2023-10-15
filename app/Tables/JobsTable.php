<?php

namespace App\Tables;

use App\Helpers\Permissions;
use App\Models\Job;
use App\Tables\RowActions\CancelJobRowAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Result;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Filters\ValueFilter;
use Okipa\LaravelTable\RowActions\EditRowAction;
use Okipa\LaravelTable\RowActions\ShowRowAction;

class JobsTable extends AbstractTableConfiguration
{
    public ?int $vClientId = null;

    protected function table(): Table
    {
        $hasJobEdit = Permissions::checkPermission(Permissions::ACL_JOB_EDIT);
        $hasJobView = Permissions::checkPermission(Permissions::ACL_JOB_VIEW);

        return Table::make()
            ->model(Job::class)
            ->query(function(Builder $query) {
                if ($this->vClientId > 0) {
                    $query = $query->where('client_id', $this->vClientId);
                }

                return $query->orderBy('id', 'DESC');
            })
            ->numberOfRowsPerPageOptions([25])
            ->rowActions(fn(Job $Job) => [
                (new ShowRowAction(route('job.view', ['codedId' => $Job->codedId]), $this->vClientId > 0))
                    ->when($hasJobView),
                (new EditRowAction(route('job.edit', ['codedId' => $Job->codedId]), $this->vClientId > 0))
                    ->when($hasJobEdit && !in_array($Job->status, [Job::STATUS_DONE, Job::STATUS_CANCEL])),
                (new CancelJobRowAction())
                    ->when($hasJobEdit && !in_array($Job->status, [Job::STATUS_DONE, Job::STATUS_CANCEL])),
            ])
            ->filters([
                new ValueFilter(
                    'Status (Todos):',
                    'status',
                    Job::JOB_STATUSES,
                    false
                ),
            ]);
    }

    protected function columns(): array
    {
        $cols = [
            Column::make('id')->title('ID')->sortable(),
            Column::make('uid')->title('PIT')->sortable()->searchable(),
        ];

        if (!$this->vClientId > 0) {
            $cols[] = Column::make('client_id')->title('Cliente')->sortable()->searchable()->format(function(Job $Job) {
                return $Job->client->name;
            });
        }

        $cols[] = Column::make('title')->title('Título')->sortable()->searchable();
        $cols[] = Column::make('status')->title('Status')->sortable()->format(function(Job $Job) {
            return $Job->statusDescription;
        });

        return $cols;
    }

    protected function results(): array
    {
        return [
            Result::make()
                ->title('Total em Andamento')
                ->format(static fn(QueryBuilder $totalRowsQuery) => $totalRowsQuery
                    ->where('status', '=', Job::STATUS_JOB)
                    ->count()),

            Result::make()
                ->title('Total em Revisão')
                ->format(static fn(QueryBuilder $totalRowsQuery) => $totalRowsQuery
                    ->where('status', '=', Job::STATUS_REVIEW)
                    ->count()),
        ];
    }
}
