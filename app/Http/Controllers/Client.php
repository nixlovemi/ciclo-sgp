<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\SysUtils;
use App\Models\Client as mClient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class Client extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const ADD_CLIENT_DATA = 'add_client_data';

    public function index()
    {
        return view('client.index');
    }

    public function view(string $codedId)
    {
        /** @var ?mClient $Client */
        $Client = mClient::getModelByCodedId($codedId);

        return view('client.register', [
            'title' => 'Visualizar',
            'type' => 'view',
            'action' => '',
            'Client' => $Client,
        ]);
    }

    public function add()
    {
        $Client = new mClient();
        $Client->fill(
            session()->pull(self::ADD_CLIENT_DATA, [])
        );

        return view('client.register', [
            'title' => 'Adicionar',
            'type' => 'add',
            'action' => route('client.add'),
            'Client' => $Client,
        ]);
    }

    public function addSave(Request $request)
    {
        $fields = $this->getClientFormFields($request);

        $Client = new mClient();
        $Client->fill($fields);
        $Client->create_user_id = SysUtils::getLoggedInUser()?->id;
        $validate = $Client->validateModel();
        if ($validate->isError()) {
            session([
                self::ADD_CLIENT_DATA => $fields
            ]);

            return $this->setNotificationRedirect(
                new ApiResponse(true, $this->getValidateMessage($validate)),
                'client.add'
            );
        }
        
        $Client->save();
        $Client->refresh();

        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Cliente inserido com sucesso!'),
            'client.edit',
            ['codedId' => $Client->codedId]
        );
    }

    public function edit(string $codedId)
    {
        /** @var ?mClient $Client */
        $Client = mClient::getModelByCodedId($codedId);

        return view('client.register', [
            'title' => 'Editar',
            'type' => 'edit',
            'action' => route('client.edit', ['codedId' => $Client?->codedId]),
            'Client' => $Client,
        ]);
    }

    public function editSave(Request $request, string $codedId)
    {
        $fields = $this->getClientFormFields($request);
        /** @var mClient $Client */
        $Client = mClient::getModelByCodedId($codedId);
        if (!$Client) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, 'Erro ao buscar cliente para edição!'),
                'client.edit',
                ['codedId' => $codedId]
            );
        }

        $Client->fill($fields);
        $validate = $Client->validateModel();
        if ($validate->isError()) {
            return $this->setNotificationRedirect(
                new ApiResponse(true, $this->getValidateMessage($validate)),
                'client.edit',
                ['codedId' => $codedId]
            );
        }
        
        $Client->update();
        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Cliente editado com sucesso!'),
            'client.edit',
            ['codedId' => $codedId]
        );
    }

    private function getClientFormFields(Request $request): array
    {
        return [
            'name' => $request->input('client-name') ?: null,
            'email' => $request->input('client-email') ?: null,
            'phone' => $request->input('client-phone') ?: null,
            'notes' => $request->input('client-notes') ?: null,
            'business_id' => $request->input('client-b-id') ?: null,
            'business_name' => $request->input('client-b-name') ?: null,
            'business_phone' => $request->input('client-b-phone') ?: null,
            'business_email' => $request->input('client-b-email') ?: null,
            'street' => $request->input('client-street') ?: null,
            'street_2' => $request->input('client-street-2') ?: null,
            'city' => $request->input('client-city') ?: null,
            'province' => $request->input('client-province') ?: null,
            'country' => $request->input('client-country') ?: null,
            'postal_code' => $request->input('client-postal-code') ?: null,
        ];
    }
}