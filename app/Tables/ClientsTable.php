<?php

namespace App\Tables;

use App\Models\Client;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Formatters\BooleanFormatter;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Filters\ValueFilter;
use Okipa\LaravelTable\RowActions\ShowRowAction;
use Okipa\LaravelTable\RowActions\EditRowAction;
use App\Tables\RowActions\ActivateRowAction;
use App\Tables\RowActions\DeactivateRowAction;
use App\Helpers\Permissions;

class ClientsTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        $hasClientEdit = Permissions::checkPermission(Permissions::ACL_CLIENT_EDIT);
        $hasClientView = Permissions::checkPermission(Permissions::ACL_CLIENT_VIEW);

        return Table::make()
            ->model(Client::class)
            ->numberOfRowsPerPageOptions([25])
            ->rowActions(fn(Client $Client) => [
                (new ActivateRowAction('active'))
                    ->when(!$Client->active && $hasClientEdit)
                    ->confirmationQuestion('Deseja marcar como ativo o cliente `' . $Client->name . '`?')
                    ->feedbackMessage(false),
                (new DeactivateRowAction('active'))
                    ->when($Client->active && $hasClientEdit)
                    ->confirmationQuestion('Deseja marcar como inativo o cliente `' . $Client->name . '`?')
                    ->feedbackMessage(false),
                (new ShowRowAction(route('client.view', ['codedId' => $Client->codedId])))
                    ->when($hasClientView),
                (new EditRowAction(route('client.edit', ['codedId' => $Client->codedId])))
                    ->when($hasClientEdit),
            ])
            ->filters([
                new ValueFilter(
                    'Ativo (Todos):',
                    'active',
                    [
                        1 => 'Sim',
                        0 => 'Não',
                    ],
                    false
                ),
            ]);
    }

    protected function columns(): array
    {
        return [
            Column::make('id')->title('ID')->sortable(),
            Column::make('name')->title('Nome')->sortable()->searchable(),
            Column::make('email')->title('E-mail')->sortable()->searchable(),
            Column::make('city')->title('Cidade')->sortable()->searchable(),
            Column::make('province')->title('Estado')->sortable()->searchable(),
            Column::make('active')->title('Ativo')->format(new BooleanFormatter()),
        ];
    }
}
