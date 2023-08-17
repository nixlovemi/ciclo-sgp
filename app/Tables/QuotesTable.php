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
            Column::make('date')->title('Data')->sortable()->format(function(Quote $Quote) {
                return SysUtils::timezoneDate($Quote->date, 'd/m/Y');
            }),
            Column::make('validity_days')->title('Validade')
                ->format(function(Quote $Quote) {
                    return $Quote->validity_days . ' dias';
                }),
            Column::make('payment_type')->title('Forma Pagamento'),
            Column::make('total')->title('Total')->format(function(Quote $Quote) {
                return $Quote->formattedTotal;
            }),
            Column::make('job')->title('Job')->format(function(Quote $Quote) {
                $html = '';
                if ($Quote->job) {
                    $href = route('job.view', ['codedId' => $Quote->job->codedId]);
                    $html = "<a href='$href' title='Ver Job linkado'><i class='fas fa-rocket'></i></a>";
                }

                return $html;
            }),
            Column::make('active')->title('Ativo')->format(new BooleanFormatter()),
        ];
    }
}
