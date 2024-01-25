<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\ApiResponse;
use App\Helpers\LocalLogger;
use App\Helpers\Pdf AS hPDF;
use App\Helpers\SysUtils;
use App\Models\Client;
use App\Models\Job as mJob;
use App\Models\JobBriefing;
use App\Models\JobInvoice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class Job extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const ADD_JOB_DATA = 'add_job_data';
    const ADD_JOB_BRIEFING_DATA = 'add_job_briefing_data';
    const ADD_JOB_INVOICE_DATA = 'add_job_invoice_data';
    const JOB_ACCORDION_ID = 'job_accordion';

    public function index()
    {
        return view('job.index');
    }

    public function view(string $codedId)
    {
        /** @var ?mJob $Client */
        $Job = mJob::getModelByCodedId($codedId);

        return view('job.register', [
            'title' => 'Visualizar Job | ' . $Job?->uid,
            'type' => 'view',
            'Job' => $Job,
        ]);
    }

    public function add()
    {
        return view('job.register', [
            'title' => 'Novo Job',
            'type' => 'add',
            'action' => route('job.doAdd'),
            'Job' => $this->getAddJobModel(),
        ]);
    }

    public function doAdd(Request $request)
    {
        $retSave = $this->saveJobFromRequest($request);
        if ($retSave->isError()) {
            return $this->setNotificationRedirect(
                $retSave,
                'job.add',
                []
            );
        }

        // all good
        $Job = $retSave->getValueFromResponse('Job');
        return $this->setNotificationRedirect(
            $retSave,
            'job.edit',
            ['codedId' => $Job?->codedId]
        );
    }

    public function edit(string $codedId)
    {
        /** @var ?mJob $Client */
        $Job = mJob::getModelByCodedId($codedId);

        return view('job.register', [
            'title' => 'Editar Job | ' . $Job?->uid,
            'type' => 'edit',
            'Job' => $Job,
            'action' => route('job.doEdit', ['codedId' => $Job->codedId]),
        ]);
    }

    public function doEdit(Request $request, string $codedId)
    {
        $retSave = $this->saveJobFromRequest(
            $request,
            mJob::getModelByCodedId($codedId)
        );
        if ($retSave->isError()) {
            return $this->setNotificationRedirect(
                $retSave,
                'job.edit',
                ['codedId' => $codedId]
            );
        }

        // all good
        $Job = $retSave->getValueFromResponse('Job');
        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Job editado com sucesso!'),
            'job.edit',
            ['codedId' => $Job->codedId]
        );
    }

    public function briefingPdf(string $codedId)
    {
        /** @var ?mJob $Client */
        $Job = mJob::getModelByCodedId($codedId);
        if (null === $Job) {
            LocalLogger::log('Job não encontrado para gerar PDF do briefing. CodedId: ' . $codedId);
            return redirect()->route('site.404');
        }

        // create new PDF document
        $pdf = new hPDF(
            'Briefing Job ' . $Job?->uid,
            'job.briefingHtmlToPdf',
            [
                'Job' => $Job
            ]
        );
        $pdf->setPortrait();
        $file = $pdf->generate("job_briefing_{$Job->id}.pdf");
        if (null === $file) {
            LocalLogger::log('Job não gerou PDF do briefing. JobId: ' . $Job->id);
            return redirect()->route('site.404');
        }

        return response()->file($file);
    }

    private function getAddJobModel(): mJob
    {
        $Job = new mJob(session()->pull(self::ADD_JOB_DATA, []));
        $Job->briefing = new JobBriefing(session()->pull(self::ADD_JOB_BRIEFING_DATA, []));

        return $Job;
    }

    private function getJobForm(Request $request, array $only=[]): array
    {
        $jobForm = [
            'codedClient' => $request->input('job-client') ?: '',
            'codedResponsible' => $request->input('job-responsible') ?: '',
            'status' => $request->input('job-status') ?: null,
            'title' => $request->input('job-title') ?: null,
            'responsible' => $request->input('job-responsible') ?: null,
            'due_date' => $request->input('job-due-date') ?: null,
        ];
        $jobForm['client_id'] = Client::getModelByCodedId($jobForm['codedClient'])?->id;

        if ($jobForm['due_date']) {
            $jobForm['due_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $jobForm['due_date'])->format('Y-m-d');
        }

        if (count($only) > 0) {
            $jobForm = SysUtils::getArrayOnlyKeys($jobForm, $only);
        }

        return $jobForm;
    }

    private function getJobBriefingForm(Request $request, array $only=[]): array
    {
        $form = [
            'objective' => $request->input('job-b-objectvie') ?: null,
            'material' => $request->input('job-b-material') ?: null,
            'technical' => $request->input('job-b-technical') ?: null,
            'content_info' => $request->input('job-b-content-info') ?: null,
            'creative_details' => $request->input('job-b-creative-det') ?: null,
            'deliverables' => $request->input('job-b-deliverables') ?: null,
            'notes' => $request->input('job-b-notes') ?: null,
        ];

        if (count($only) > 0) {
            $form = SysUtils::getArrayOnlyKeys($form, $only);
        }

        return $form;
    }

    private function getJobInvoiceForm(Request $request, array $only=[]): array
    {
        $form = [
            'invoice_number' => $request->input('jinvoice-number') ?: null,
            'invoice_date' => $request->input('jinvoice-date') ?: null,
            'due_date' => $request->input('jinvoice-due') ?: null,
            'total' => $request->input('jinvoice-total') ?: null,
            'file' => $request->file('jinvoice-path') ?: null,
        ];

        if ($form['invoice_date']) {
            $form['invoice_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $form['invoice_date'])->format('Y-m-d');
        }

        if ($form['due_date']) {
            $form['due_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $form['due_date'])->format('Y-m-d');
        }

        if (null !== $form['total']) {
            $form['total'] = SysUtils::formatNumberToDb($form['total'], 2);
        }

        if (count($only) > 0) {
            $form = SysUtils::getArrayOnlyKeys($form, $only);
        }

        return $form;
    }

    private function getQuoteForm(Request $request, array $only=[]): array
    {
        $form = [
            'date' => $request->input('quote-date') ?: null,
            'validity_days' => $request->input('quote-validity') ?: null,
            'payment_type' => $request->input('quote-payment-type') ?: null,
            'payment_type_memo' => $request->input('quote-pt-memo') ?: null,
            'notes' => $request->input('quote-notes') ?: null,
        ];

        if ($form['date']) {
            $form['date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $form['date'])->format('Y-m-d');
        }

        if (count($only) > 0) {
            $form = SysUtils::getArrayOnlyKeys($form, $only);
        }

        return $form;
    }

    private function saveJobFromRequest(Request $request, ?mJob $Job = null): ApiResponse
    {
        // Determine if it's an edit or a new job
        $isEdit = $Job !== null;

        // Initialize Job and JobBriefing objects
        if (!$Job) {
            $Job = new mJob();
        }
        $JobBriefing = $Job->briefing ?: new JobBriefing();
        $JobInvoice = $Job->invoice ?: new JobInvoice();

        // Form data
        $fields = $isEdit ? [] : ['client_id'];
        $jobData = $this->getJobForm($request, array_merge($fields, ['status', 'title', 'due_date', 'responsible']));
        $jobBriefingForm = $this->getJobBriefingForm($request, ['objective', 'material', 'technical', 'content_info', 'creative_details', 'deliverables', 'notes']);
        $jobQuoteForm = $this->getQuoteForm($request, ['date', 'validity_days', 'payment_type', 'payment_type_memo', 'notes']);
        $jobInvoiceForm = $this->getJobInvoiceForm($request, ['invoice_number', 'invoice_date', 'due_date', 'total', 'file']);

        // Store form data in sessions
        session([
            self::ADD_JOB_DATA => $jobData,
            self::ADD_JOB_BRIEFING_DATA => $jobBriefingForm,
            self::ADD_JOB_INVOICE_DATA => $jobInvoiceForm,
        ]);

        // Validate and save Job
        $Job->fill($jobData);
        $validate = $Job->validateModel();
        if ($validate->isError()) {
            return new ApiResponse(true, ApiResponse::getValidateMessage($validate));
        }

        try {
            if ($isEdit) {
                $Job->update();
            } else {
                $Job->save();
            }
            $Job->refresh();
        } catch (\Throwable $th) {
            if (!$isEdit) {
                $Job->delete();
            }
            LocalLogger::log('Erro ao salvar job! Msg: ' . $th->getMessage());
            return new ApiResponse(true, 'Erro ao salvar job!');
        }

        // Save JobBriefing
        $JobBriefing->job_id = $Job->id;
        $JobBriefing->fill($jobBriefingForm);
        try {
            if ($isEdit) {
                $JobBriefing->update();
            } else {
                $JobBriefing->save();
            }
        } catch (\Throwable $th) {
            if (!$isEdit) {
                $Job->delete();
                $JobBriefing->delete();
            }
            LocalLogger::log('Erro ao salvar job - salvar briefing! Msg: ' . $th->getMessage());
            return new ApiResponse(true, 'Erro ao salvar job!');
        }

        // Update Quote (if it exists)
        if ($isEdit && $Job->quote) {
            $Quote = $Job->quote;
            $Quote->fill($jobQuoteForm);

            $validate = $Quote->validateModel();
            if ($validate->isError()) {
                return new ApiResponse(true, ApiResponse::getValidateMessage($validate));
            }

            try {
                $Quote->update();
                $Job->refresh();
            } catch (\Throwable $th) {
                LocalLogger::log('Erro ao salvar job - salvar quote! Msg: ' . $th->getMessage());
                return new ApiResponse(true, 'Erro ao salvar job!');
            }
        }

        // invoice
        $JobInvoice->job_id = $Job->id;
        $JobInvoice->fill($jobInvoiceForm);
        try {
            if ($isEdit) {
                $JobInvoice->update();
            } else {
                $JobInvoice->save();
            }

            $file = $jobInvoiceForm['file'];
            if ($file) {
                $retAdd = $JobInvoice->addFile($file);
                if ($retAdd->isError()) {
                    throw new Exception($retAdd->getMessage());
                }
                $JobInvoice->update();
            }
        } catch (\Throwable $th) {
            if (!$isEdit) {
                $Job->delete();
                $JobBriefing->delete();
                $JobInvoice->delete();
            }
            LocalLogger::log('Erro ao salvar job - salvar invoice! Msg: ' . $th->getMessage());
            return new ApiResponse(true, 'Erro ao salvar job!');
        }

        // Clear session data
        session([
            self::ADD_JOB_DATA => [],
            self::ADD_JOB_BRIEFING_DATA => [],
            self::ADD_JOB_INVOICE_DATA => [],
        ]);

        $Job->refresh();
        return new ApiResponse(false, 'Job adicionado com sucesso!', [
            'Job' => $Job
        ]);
    }
}