<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\LocalLogger;
use App\Helpers\Pdf AS hPDF;
use App\Helpers\SysUtils;
use App\Http\Controllers\Job as cJob;
use App\Models\Client;
use App\Models\Job;
use App\Models\Quote as mQuote;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;

class Quote extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const ADD_QUOTE_DATA = 'add_quote_data';

    public function index()
    {
        return view('quote.index');
    }

    public function add(?string $codedId='')
    {
        $Quote = mQuote::getModelByCodedId($codedId ?? '');
        return view('quote.show', [
            'Quote' => $Quote ?? $this->getAddQuoteModel(),
            'type' => $Quote?->id > 0 ? 'edit': 'add',
            'disabled' => false
        ]);
    }

    public function doAdd(Request $request)
    {
        $qcid = $request->input('qcid') ?: '';
        $quote = mQuote::getModelByCodedId($qcid);
        $quoteForm = $this->getQuoteFormFields($request);

        if (!$quote instanceof mQuote) {
            $quote = new mQuote();
        }

        try {
            $quote->fill($quoteForm);
            $validationResult = $quote->validateModel();

            if ($validationResult->isError()) {
                $notification = new ApiResponse(true, ApiResponse::getValidateMessage($validationResult));
                $routeName = 'quote.add';
                $routeParams = ['codedId' => $quote->codedId ?? ''];

                return $this->setNotificationRedirect($notification, $routeName, $routeParams);
            }

            if ($quote->id > 0) {
                $quote->update();
                $retMsg = 'alterado';
            } else {
                $quote->save();
                $retMsg = 'adicionado';
            }
            $quote->refresh();

            session([self::ADD_QUOTE_DATA => []]);

            $successMessage = "Orçamento {$retMsg} com sucesso!";
            $routeName = 'quote.add';
            $routeParams = ['codedId' => $quote->codedId];

            return $this->setNotificationRedirect(new ApiResponse(false, $successMessage), $routeName, $routeParams);
        } catch (\Throwable $exception) {
            LocalLogger::log('Erro ao adicionar Orçamento! Msg: ' . $exception->getMessage());
            return $this->returnResponse(true, 'Erro ao adicionar Orçamento!', [], Response::HTTP_OK);
        }
    }

    public function getLinkToJobHtml(Request $request)
    {
        $json = (bool) $request->input('json') ?: false;
        $jobCodedId = $request->input('jobCodedId') ?: '';
        /** @var ?Job $Job */
        $Job = Job::getModelByCodedId($jobCodedId);

        $view = view('quote.linkToJob', [
            'Job' => $Job
        ]);

        if (true === $json) {
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

    public function saveLinkToJobHtml(Request $request)
    {
        $jobCodedId = $request->input('jcid') ?: '';
        $quoteCodedId = $request->input('quote-to-link') ?: '';
        $disabled = $request->input('formDisabled') ?: 0;

        $Quote = mQuote::getModelByCodedId($quoteCodedId);
        if (!$Quote instanceof mQuote) {
            return $this->returnResponse(true, 'Orçamento não encontrado para fazer o vínculo!', [], Response::HTTP_OK);
        }

        $Job = Job::getModelByCodedId($jobCodedId);
        $retLink = $Quote->linkJob($Job);
        $html = $retLink->isError() ? '': $this->getJobQuoteCardView($Job, $disabled)?->render();

        return $this->returnResponse($retLink->isError(), $retLink->getMessage(), [
            'html' => $html
        ], Response::HTTP_OK);
    }

    public function getQuoteItemsHtml(Request $request)
    {
        $json = (bool) $request->input('json') ?: false;
        $type = $request->input('type') ?: '';
        $disabled = $request->input('disabled') ?: 'false';
        $quoteCodedId = $request->input('quoteCodedId') ?: '';
        /** @var ?mQuote $Quote */
        $Quote = mQuote::getModelByCodedId($quoteCodedId);
        
        $view = view('quote.showDiv', [
            'Quote' => $Quote,
            'type' => $type,
            'disabled' => $disabled == 'true' #comes as string
        ]);

        if (true === $json) {
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

    public function addFromJob(Request $request)
    {
        $disabled = $request->input('formDisabled') ?: 0;
        $jobCodedId = $request->input('jcid') ?: '';

        $Job = Job::getModelByCodedId($jobCodedId);
        if (!$Job instanceof Job) {
            return $this->returnResponse(true, 'Job não encontrado para desvincular o orçamento!', [], Response::HTTP_OK);
        }
        
        try {
            $Quote = new mQuote();
            $Quote->date = date('Y-m-d');
            $Quote->create_user_id = SysUtils::getLoggedInUser()?->id;
            $Quote->client_id = $Job->client_id;
            $Quote->save();
            $Quote->refresh();

            $Job->quote_id = $Quote->id;
            $Job->update();
            $Job->refresh();

            $view = $this->getJobQuoteCardView($Job, $disabled);
            return $this->returnResponse(false, 'Orçamento adicionado com sucesso!', [
                'html' => $view->render(),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            LocalLogger::log('Erro ao adicionar o orçamento no Job! Msg: ' . $th->getMessage());
            return $this->returnResponse(true, 'Erro ao adicionar o orçamento no Job!', [], Response::HTTP_OK);
        }
    }

    public function removeFromJob(Request $request)
    {
        $deleteQuote = $request->input('deleteQuote') ?: 'false';
        $disabled = $request->input('formDisabled') ?: 0;
        $jobCodedId = $request->input('jcid') ?: '';
        $Job = Job::getModelByCodedId($jobCodedId);
        if (!$Job instanceof Job) {
            return $this->returnResponse(true, 'Job não encontrado para desvincular o orçamento!', [], Response::HTTP_OK);
        }

        $retUnlink = $Job->unlinkQuote();
        if ($retUnlink->isError()) {
            return $this->returnResponse(true, $retUnlink->getMessage(), [], Response::HTTP_OK);
        }

        // all good
        if ('true' == $deleteQuote || true === $deleteQuote) {
            $quoteId = $retUnlink->getValueFromResponse('quoteId') ?? '';
            if ($Quote = mQuote::find($quoteId)) {
                $Quote->active = false;
                $Quote->update();
            }
        }

        $view = $this->getJobQuoteCardView($Job, $disabled);
        return $this->returnResponse(false, $retUnlink->getMessage(), [
            'html' => $view->render(),
        ], Response::HTTP_OK);
    }

    public function pdf(string $codedId)
    {
        /** @var ?mQuote $Quote */
        $Quote = mQuote::getModelByCodedId($codedId);
        if (null === $Quote) {
            LocalLogger::log('Orçamento não encontrado para gerar PDF. CodedId: ' . $codedId);
            return redirect()->route('site.404');
        }

        // create new PDF document
        $pdf = new hPDF(
            'Orçamento ' . $Quote?->id,
            'quote.htmlToPdf',
            [
                'Quote' => $Quote
            ]
        );
        $pdf->setPortrait();
        $file = $pdf->generate("quote_{$Quote->id}.pdf");
        if (null === $file) {
            LocalLogger::log('Orçamento não gerou PDF. QuoteId: ' . $Quote->id);
            return redirect()->route('site.404');
        }

        return response()->file($file);
    }

    private function getJobQuoteCardView(Job $Job, bool $disabled=true): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('job.partials.quoteCard', [
            'Job' => $Job,
            'dataParent' => cJob::JOB_ACCORDION_ID,
            'disabled' => $disabled
        ]);
    }

    private function getAddQuoteModel(): mQuote
    {
        return new mQuote(session()->pull(self::ADD_QUOTE_DATA, []));
    }

    private function getQuoteFormFields(Request $request): array
    {
        $fields = [
            'date' => $request->input('quote-date') ?: null,
            'create_user_id' => SysUtils::getLoggedInUser()?->id ?? null,
            'client_id_coded' => $request->input('quote-client') ?: '',
            'validity_days' => $request->input('quote-validity') ?: null,
            'payment_type' => $request->input('quote-payment-type') ?: null,
            'payment_type_memo' => $request->input('quote-pt-memo') ?: null,
            'notes' => $request->input('quote-notes') ?: null,
        ];

        if ($fields['date']) {
            $fields['date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $fields['date'])->format('Y-m-d');
        }

        if ($fields['client_id_coded']) {
            $fields['client_id'] = Client::getModelByCodedId($fields['client_id_coded'])?->id ?? null;
        }

        session([
            self::ADD_QUOTE_DATA => $fields
        ]);
        return $fields;
    }
}