<?php

namespace App\Http\Controllers;

use App\Helpers\LocalLogger;
use App\Helpers\SysUtils;
use App\Models\Job;
use App\Models\JobFile as mJobFile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JobFile extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const ADD_JOB_DATA = 'add_job_data';
    const ADD_JOB_BRIEFING_DATA = 'add_job_briefing_data';

    public function add(string $jobCodedId, bool $json, string $jobSection = null)
    {
        $Job = Job::getModelByCodedId($jobCodedId);
        $view = view('jobFile.add', [
            'Job' => $Job,
            'jobSection' => $jobSection,
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

    public function doAdd(Request $request)
    {
        $requestData = $request->only(['jcid', 'jsec', 'jf-title', 'jf-tipo', 'jf-url', 'jf-file']);
        $jobId = Job::getModelByCodedId($requestData['jcid'])?->id;

        $JobFile = new mJobFile([
            'job_id' => $jobId,
            'title' => $requestData['jf-title'],
            'type' => $requestData['jf-tipo'],
            'job_section' => $this->handleJobSection($requestData),
            'url' => $requestData['jf-url'],
        ]);

        if ($JobFile->type === mJobFile::TYPE_FILE) {
            $file = $requestData['jf-file'];

            if (!$file) {
                return $this->returnResponse(true, 'Nenhum arquivo selecionado!', [], Response::HTTP_OK);
            }

            $retAdd = $JobFile->addFile($file);
            if ($retAdd->isError()) {
                return $this->returnResponse(true, $retAdd->getMessage(), [], Response::HTTP_OK);
            }

            $dataJobFile = $retAdd->getValueFromResponse('JobFile');
            $JobFile = $dataJobFile ?? $JobFile;
        }

        $retValidate = $JobFile->validateModel();
        if ($retValidate->isError()) {
            mJobFile::deleteModelUrlFile($JobFile);
            return $this->returnResponse(true, $this->getValidateMessage($retValidate), [], Response::HTTP_OK);
        }

        // all good
        try {
            $JobFile->save();
            $JobFile->refresh();

            return $this->returnResponse(false, 'Arquivo adicionado com sucesso!', [
                'JobFileCodedId' => $JobFile->codedId,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            mJobFile::deleteModelUrlFile($JobFile);
            LocalLogger::log('Erro ao salvar JobFile! Msg: ' . $th->getMessage());
            return $this->returnResponse(true, 'Erro ao adicionar arquivo!', [], Response::HTTP_OK);
        }
    }

    private function handleJobSection(array $requestData): ?string
    {
        $jobSection = $requestData['jsec'];

        if (empty($jobSection)) {
            return null;
        }

        // if user is editor, only allow to add briefing final review
        $User = SysUtils::getLoggedInUser();
        if (
            null !== $User &&
            $User->isEditor() &&
            $jobSection !== mJobFile::JOB_SECTION_BRIEFING_FINAL_REVIEW
        ) {
            return mJobFile::JOB_SECTION_BRIEFING_FINAL_REVIEW;
        }

        return $jobSection;
    }
}