<?php

namespace App\Tables;

use App\Helpers\Permissions;
use App\Models\ServiceItem;
use App\Tables\RowActions\ActivateRowAction;
use App\Tables\RowActions\DeactivateRowAction;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Filters\ValueFilter;
use Okipa\LaravelTable\Formatters\BooleanFormatter;
use Okipa\LaravelTable\RowActions\EditRowAction;
use Okipa\LaravelTable\RowActions\ShowRowAction;
use Okipa\LaravelTable\Table;

class ServiceItemTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        $hasServiceItemEdit = Permissions::checkPermission(Permissions::ACL_SERVICE_ITEM_EDIT);
        $hasServiceItemView = Permissions::checkPermission(Permissions::ACL_SERVICE_ITEM_VIEW);

        return Table::make()
            ->model(ServiceItem::class)
            ->numberOfRowsPerPageOptions([25])
            ->rowActions(fn(ServiceItem $ServiceItem) => [
                (new ActivateRowAction('active'))
                    ->when(!$ServiceItem->active && $hasServiceItemEdit)
                    ->confirmationQuestion('Deseja marcar como ativo o item `' . $ServiceItem->description . '`?')
                    ->feedbackMessage(false),
                (new DeactivateRowAction('active'))
                    ->when($ServiceItem->active && $hasServiceItemEdit)
                    ->confirmationQuestion('Deseja marcar como inativo o item `' . $ServiceItem->description . '`?')
                    ->feedbackMessage(false),
                (new ShowRowAction(route('serviceItems.view', ['codedId' => $ServiceItem->codedId])))
                    ->when($hasServiceItemView),
                (new EditRowAction(route('serviceItems.edit', ['codedId' => $ServiceItem->codedId])))
                    ->when($hasServiceItemEdit),
            ])
            ->filters([
                new ValueFilter(
                    'Status (Todos):',
                    'active',
                    [
                        1 => 'Ativos',
                        0 => 'Inativos'
                    ],
                    false
                ),
            ]);
    }

    protected function columns(): array
    {
        return [
            Column::make('id')->title('ID')->sortable(),
            Column::make('description')->title('Descrição')->sortable()->searchable(),
            Column::make('price')->title('Valor')->sortable()->format(function(ServiceItem $ServiceItem) {
                return $ServiceItem->currencyPrice;
            }),
            Column::make('active')->title('Ativo')->format(new BooleanFormatter()),
        ];
    }
}
