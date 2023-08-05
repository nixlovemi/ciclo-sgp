<?php

namespace App\Tables;

use App\Models\User;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Formatters\BooleanFormatter;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Filters\ValueFilter;
use Okipa\LaravelTable\RowActions\ShowRowAction;
use Okipa\LaravelTable\RowActions\EditRowAction;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\RowActions\ActivateRowAction;
use App\Tables\RowActions\DeactivateRowAction;
use App\Helpers\Permissions;

class UsersTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        $hasUserEdit = Permissions::checkPermission(Permissions::ACL_USER_EDIT);
        $hasUserView = Permissions::checkPermission(Permissions::ACL_USER_VIEW);

        return Table::make()
            ->model(User::class)
            ->numberOfRowsPerPageOptions([25])
            ->rowActions(fn(User $User) => [
                (new ActivateRowAction('active'))
                    ->when(!$User->active && $hasUserEdit)
                    ->confirmationQuestion('Deseja marcar como ativo o usuário `' . $User->name . '`?')
                    ->feedbackMessage(false),
                (new DeactivateRowAction('active'))
                    ->when($User->active && $hasUserEdit)
                    ->confirmationQuestion('Deseja marcar como inativo o usuário `' . $User->name . '`?')
                    ->feedbackMessage(false),
                (new ShowRowAction(route('user.view', ['codedId' => $User->codedId])))
                    ->when($hasUserView),
                (new EditRowAction(route('user.edit', ['codedId' => $User->codedId])))
                    ->when($hasUserEdit),
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
                new ValueFilter(
                    'Cargo (Todos):',
                    'role',
                    User::USER_ROLES,
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
            Column::make('role')->title('Função')->sortable()->format(function(User $User) {
                return User::USER_ROLES[$User->role] ?? $User->role;
            }),
            Column::make('active')->title('Ativo')->format(new BooleanFormatter()),
        ];
    }
}
