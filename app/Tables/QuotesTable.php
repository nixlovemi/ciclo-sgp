<?php

namespace App\Tables;

use App\Helpers\Permissions;
use App\Helpers\SysUtils;
use App\Tables\RowActions\ActivateRowAction;
use App\Tables\RowActions\DeactivateRowAction;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Filters\ValueFilter;
use Okipa\LaravelTable\Formatters\BooleanFormatter;
use Okipa\LaravelTable\RowActions\EditRowAction;
use Okipa\LaravelTable\RowActions\RedirectRowAction;
use Okipa\LaravelTable\Table;

class QuotesTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        $hasQuoteEdit = Permissions::checkPermission(Permissions::ACL_QUOTE_EDIT);

        return Table::make()
            ->model(Quote::class)
            ->numberOfRowsPerPageOptions([25])
            ->rowActions(fn(Quote $Quote) => [
                (new ActivateRowAction('active'))
                    ->when(!$Quote->active && $hasQuoteEdit)
                    ->confirmationQuestion('Deseja marcar como ativo o orçamento `' . $Quote->id . '`?')
                    ->feedbackMessage(false),
                (new DeactivateRowAction('active'))
                    ->when($Quote->active && $hasQuoteEdit)
                    ->confirmationQuestion('Deseja marcar como inativo o orçamento `' . $Quote->id . '`?')
                    ->feedbackMessage(false),
                (new EditRowAction(route('quote.add', ['codedId' => $Quote->codedId])))
                    ->when($hasQuoteEdit && $Quote->active),
                (new RedirectRowAction(
                    route('quote.pdf', ['codedId' => $Quote->codedId]),
                    'PDF',
                    '<i class="fas fa-file-pdf"></i>',
                    ['link-danger'],
                    null,
                    null,
                    true
                ))->when($Quote->active),
            ])
            ->filters([
                new ValueFilter(
                    'Status (Todos):',
                    'active',
                    [0=>'Inativo', 1=>'Ativo'],
                    false
                ),
            ]);
    }

    protected function columns(): array
    {
        return [
            Column::make('id')->title('ID')->sortable()->searchable(),
            Column::make('client_id')->title('Cliente')->sortable()
                ->searchable(function($query, string $searchBy) {
                    return $query->whereHas('client', function (Builder $query) use ($searchBy) {
                        $query->where('clients.name', 'LIKE', '%' . $searchBy . '%');
                    });
                })->format(function(Quote $Quote) {
                    return $Quote->client->name;
                }),
            Column::make('pit')->title('PIT')
                ->searchable(function($query, string $searchBy) {
                    return $query->whereHas('job', function (Builder $query) use ($searchBy) {
                        $query->where('jobs.uid', 'LIKE', '%' . $searchBy . '%');
                    });
                })
                ->format(function(Quote $Quote) {
                    $html = '';
                    if ($Quote->job) {
                        $href = route('job.view', ['codedId' => $Quote->job->codedId]);
                        $html = "<a href='$href' title='Ver Job linkado'>{$Quote->job->uid}</a>";
                    }

                    return $html;
                }),
            Column::make('total')->title('Total')->format(function(Quote $Quote) {
                return $Quote->formattedTotal;
            }),
            Column::make('has_invoice')->title('Faturado')->format(function(Quote $Quote) {
                // doenst have invoice
                $html = '<span class="text-danger"><i class="fa-solid fas fa-times-circle text-danger fa-fw"></i></span>';

                if ($Quote->job?->invoice && $Quote->job?->invoice?->invoice_date != '') {
                    // has invoice
                    $html = '<span class="text-success"><i class="fa-solid fas fa-check-circle text-success fa-fw"></i></span>';
                }

                return $html;
            }),
            Column::make('active')->title('Ativo')->format(new BooleanFormatter()),
        ];
    }
}
