<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ApiResponse;
use App\View\Components\Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return JSON response - API
     */
    protected function returnResponse (
        bool $error,
        string $message,
        array $data,
        ?int $responseCode
    ) {
        $ret = new ApiResponse(
            $error,
            ($error && empty($message)) ? 'Erro na requisição': $message,
            $data
        );

        if (is_null($responseCode)) {
            $responseCode = ($error) ? Response::HTTP_INTERNAL_SERVER_ERROR: Response::HTTP_OK;
        }
        
        return response()->json($ret->getArrayResponse(), $responseCode);
    }

    protected function getValidateMessage(ApiResponse $validate): string
    {
        $arrRet = $validate->getArrayResponse();
        return $arrRet[ApiResponse::KEY_DATA]['messages'] ?? $validate->getMessage();
    }

    protected function setNotificationRedirect(
        ApiResponse $response,
        string $routeName,
        array $routeParams = []
    ): \Illuminate\Http\RedirectResponse {
        $notifMethod = ($response->isError()) ? 'setWarning': 'setSuccess';
        $notifTitle = ($response->isError()) ? 'Aviso!': 'Sucesso!';

        Notification::{$notifMethod}($notifTitle, $response->getMessage());
        return redirect()->route($routeName, $routeParams);
    }
}
