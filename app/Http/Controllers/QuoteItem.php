<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\LocalLogger;
use App\Helpers\SysUtils;
use App\Models\Quote;
use App\Models\QuoteItem as mQuoteItem;
use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;

class QuoteItem extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function add(Request $request)
    {
        $quoteCodedId = $request->input('qcid') ?: '';
        $quoteItemCodedId = $request->input('qicid') ?: '';
        $json = $request->input('json') ?: 'false';
        
        $view = view('quoteItem.add', [
            'Quote' => Quote::getModelByCodedId($quoteCodedId),
            'QuoteItem' => mQuoteItem::getModelByCodedId($quoteItemCodedId),
        ]);

        if (true === (bool) $json) {
            return $this->returnResponse(
                false,
                'HTML retornado com sucesso!',
                [
                    'html' => $view->render()
                ],
                Response::HTTP_OK
            );
        }

        return $view;
    }

    public function doAdd(Request $request)
    {
        $requestData = $request->only(['qcid', 'qicid']);
        $quote = Quote::getModelByCodedId($requestData['qcid'] ?? '');
        $quoteItem = mQuoteItem::getModelByCodedId($requestData['qicid'] ?? '');

        if (!$quoteItem instanceof mQuoteItem) {
            $quoteItem = new mQuoteItem();
        }

        $quoteForm = $this->getQuoteItemFormFields($request);
        $quoteItem->fill($quoteForm);
        $quoteItem->quote_id = optional($quote)->id;
        $quoteItem->item_id = optional($quoteForm['ServiceItem'])->id;

        try {
            $validationResult = $quoteItem->validateModel();
            if ($validationResult->isError()) {
                return $this->returnResponse(true, ApiResponse::getValidateMessage($validationResult), [], Response::HTTP_OK);
            }

            $quoteItem->save();
            $quoteItem->refresh();

            return $this->returnResponse(false, 'Item do orçamento adicionado!', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            LocalLogger::log('Erro ao salvar item do orçamento! Msg: ' . $exception->getMessage());
            return $this->returnResponse(true, 'Erro ao salvar item do orçamento!', [], Response::HTTP_OK);
        }
    }

    private function getQuoteItemFormFields(Request $request): array
    {
        $fields = [
            'quantity' => $request->input('qi-qty') ?: null,
            'type' => $request->input('qi-type') ?: null,
            'price' => $request->input('qi-price') ?: null,
        ];
        $fields['ServiceItem'] = ServiceItem::getModelByCodedId($request->input('qi-item') ?: '');

        if (null !== $fields['quantity']) {
            $fields['quantity'] = SysUtils::formatNumberToDb($fields['quantity'], 2);
        }
        if (null !== $fields['price']) {
            $fields['price'] = SysUtils::formatNumberToDb($fields['price'], 2);
        }

        return $fields;
    }
}