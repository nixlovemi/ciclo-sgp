<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\LocalLogger;
use App\Helpers\SysUtils;
use App\Models\ServiceItem as mServiceItem;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ServiceItem extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const ADD_SERVICE_ITEM_DATA = 'add_service_item_data';

    public function index()
    {
        return view('serviceItem.index');
    }

    public function view(string $codedId)
    {
        /** @var ?mServiceItem $User */
        $ServiceItem = mServiceItem::getModelByCodedId($codedId);

        return view('serviceItem.register', [
            'title' => 'Visualizar',
            'type' => 'view',
            'action' => '',
            'ServiceItem' => $ServiceItem,
        ]);
    }

    public function add()
    {
        return view('serviceItem.register', [
            'title' => 'Novo Item',
            'type' => 'add',
            'action' => route('serviceItems.add.save'),
            'ServiceItem' => $this->getAddServiceItemModel(),
        ]);
    }

    public function addSave(Request $request)
    {
        $retSave = $this->saveServiceItemFromRequest($request);
        if ($retSave->isError()) {
            return $this->setNotificationRedirect(
                $retSave,
                'serviceItems.add',
                []
            );
        }

        // all good
        $ServiceItem = $retSave->getValueFromResponse('ServiceItem');
        return $this->setNotificationRedirect(
            $retSave,
            'serviceItems.edit',
            ['codedId' => $ServiceItem?->codedId]
        );
    }

    public function edit(string $codedId)
    {
        /** @var ?mServiceItem $User */
        $ServiceItem = mServiceItem::getModelByCodedId($codedId);

        return view('serviceItem.register', [
            'title' => 'Editar',
            'type' => 'edit',
            'action' => route('serviceItems.edit.save', ['codedId' => $ServiceItem?->codedId]),
            'ServiceItem' => $ServiceItem,
        ]);
    }

    public function editSave(Request $request, string $codedId)
    {
        $retSave = $this->saveServiceItemFromRequest(
            $request,
            mServiceItem::getModelByCodedId($codedId)
        );
        if ($retSave->isError()) {
            return $this->setNotificationRedirect(
                $retSave,
                'serviceItems.edit',
                ['codedId' => $codedId]
            );
        }

        // all good
        $ServiceItem = $retSave->getValueFromResponse('ServiceItem');
        return $this->setNotificationRedirect(
            new ApiResponse(false, 'Item editado com sucesso!'),
            'serviceItems.edit.save',
            ['codedId' => $ServiceItem->codedId]
        );
    }

    private function getServiceItemFormFields(Request $request): array
    {
        $fields = [
            'description' => $request->input('si-description') ?: null,
            'currency' => $request->input('si-currency') ?: null,
            'price' => $request->input('si-price') ?: null,
        ];

        if (null !== $fields['price']) {
            $fields['price'] = SysUtils::formatNumberToDb($fields['price'], 2);
        }

        return $fields;
    }

    private function getAddServiceItemModel(): mServiceItem
    {
        return new mServiceItem(session()->pull(self::ADD_SERVICE_ITEM_DATA, []));
    }

    private function saveServiceItemFromRequest(Request $request, ?mServiceItem $ServiceItem = null): ApiResponse
    {
        $isEdit = true;
        if (null === $ServiceItem) {
            $ServiceItem = new mServiceItem();
            $isEdit = false;
        }

        // Form data =====================
        $fields = $this->getServiceItemFormFields($request);
        session([
            self::ADD_SERVICE_ITEM_DATA => $fields
        ]);
        // ===============================

        $ServiceItem->fill($fields);
        $validate = $ServiceItem->validateModel();
        if ($validate->isError()) {
            return new ApiResponse(true, ApiResponse::getValidateMessage($validate));
        }

        try {
            $retSave = ($isEdit) ? $ServiceItem->update(): $ServiceItem->save();
            $ServiceItem->refresh();
        } catch (\Throwable $th) {
            if (!$isEdit) {
                $ServiceItem->delete();
            }
            LocalLogger::log('Erro ao salvar item! Msg: ' . $th->getMessage());
            return new ApiResponse(true, 'Erro ao salvar item!');
        }

        // all good
        session([
            self::ADD_SERVICE_ITEM_DATA => [],
        ]);
        $ServiceItem->refresh();
        return new ApiResponse(false, 'Item adicionado com sucesso!', [
            'ServiceItem' => $ServiceItem
        ]);
    }
}
