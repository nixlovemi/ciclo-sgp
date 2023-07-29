<?php

namespace App\Tables;

use App\Models\Client;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Formatters\BooleanFormatter;
use Okipa\LaravelTable\Table;

class ClientsTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        return Table::make()
            ->model(Client::class)
            ->numberOfRowsPerPageOptions([25]);
    }

    protected function columns(): array
    {
        return [
            Column::make('id')->title('ID')->sortable(),
            Column::make('name')->title('Nome')->sortable()->searchable(),
            Column::make('email')->title('E-mail')->sortable()->searchable(),
            Column::make('city')->title('Cidade')->sortable()->searchable(),
            Column::make('province')->title('Estado')->sortable()->searchable(),
            Column::make('active')->title('Ativo')->searchable()->format(new BooleanFormatter()),
        ];
    }
}
