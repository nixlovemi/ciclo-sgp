<?php

namespace App\Tables;

use App\Helpers\Permissions;
use App\Helpers\SysUtils;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Tables\HeadActions\OpenModalHeadAction;
use App\Tables\RowActions\OpenModalRowAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Result;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\RowActions\DestroyRowAction;

class QuoteItemsTable extends AbstractTableConfiguration
{
    public int $vQuoteId;
    public bool $vDisabled;
    private Quote $Quote;

    protected function table(): Table
    {
        $this->init();
        $hasQuoteEdit = Permissions::checkPermission(Permissions::ACL_QUOTE_EDIT);

        return Table::make()
            ->model(QuoteItem::class)
            ->query(function(Builder $query) {
                return $query
                    ->select('quote_items.*')
                    ->where('quote_items.quote_id', '=', $this->vQuoteId ?? 0)
                    ->orderBy('id', 'ASC');
            })
            ->numberOfRowsPerPageOptions([15])
            ->rowActions(fn(QuoteItem $quoteItem) => [
                (new OpenModalRowAction(
                    'Editar',
                    route('quoteItem.add', [
                        'qcid' => $this->Quote->codedId,
                        'qicid' => $quoteItem->codedId,
                        'json' => 1
                    ]),
                    '<i class="fa-solid fas fa-pencil-alt fa-fw"></i>'
                ))->when(!$this->vDisabled && $hasQuoteEdit),
                (new DestroyRowAction())
                    ->confirmationQuestion(__('Confirma a exclusão do item ":item"?', [
                        'item' => $quoteItem->serviceItem->description,
                    ]))
                    ->feedbackMessage(false)
                    ->when(!$this->vDisabled && $hasQuoteEdit),
            ])
            ->headAction(
                (new OpenModalHeadAction(route('quoteItem.add', ['qcid' => $this->Quote->codedId, 'json' => true]), 'Adicionar', '<i class="fas fa-plus"></i>', []))
                    ->when(!$this->vDisabled && $hasQuoteEdit)
            );
    }

    protected function columns(): array
    {
        return [
            Column::make('item_id')
                ->title('Item')
                ->searchable(function($query, string $searchBy) {
                    return $query->whereHas('serviceItem', function (Builder $query) use ($searchBy) {
                        $query->where('service_items.description', 'LIKE', '%' . $searchBy . '%');
                    });
                })
                ->format(function(QuoteItem $QuoteItem) {
                    return $QuoteItem->serviceItem->description;
                }),
            Column::make('quantity')
                ->title('Qtde')
                ->searchable()
                ->format(function(QuoteItem $QuoteItem) {
                    return $QuoteItem->formattedQuantity;
                }),
            Column::make('type')
                ->title('Unid.')
                ->searchable(),
            Column::make('price')
                ->title('Valor')
                ->format(function(QuoteItem $QuoteItem) {
                    return $QuoteItem->currencyPrice;
                }),
            Column::make('discount')
                ->title('Desconto')
                ->format(function(QuoteItem $QuoteItem) {
                    return $QuoteItem->currencyDiscount;
                }),
            Column::make('total')
                ->title('Total')
                ->format(function(QuoteItem $QuoteItem) {
                    return $QuoteItem->currencyTotal;
                }),
        ];
    }

    protected function results(): array
    {
        $Quote = $this->Quote;

        return [
            Result::make()
                ->title('Total Desconto')
                ->format(function(QueryBuilder $totalRowsQuery) use ($Quote) {
                    return SysUtils::formatCurrencyBr($Quote->totalDiscount, 2, $Quote->currencySymbol);
                }),

            Result::make()
                ->title('Total Geral')
                ->format(function(QueryBuilder $totalRowsQuery) use ($Quote) {
                    return SysUtils::formatCurrencyBr($Quote->total, 2, $Quote->currencySymbol);
                })
        ];
    }

    private function init(): void
    {
        $this->Quote = Quote::find($this->vQuoteId);
    }
}
