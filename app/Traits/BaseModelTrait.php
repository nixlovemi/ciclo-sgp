<?php

namespace App\Traits;

use App\Helpers\SysUtils;

trait BaseModelTrait {
    final static function getModelByCodedId(string $codedId): ?\Illuminate\Database\Eloquent\Model
    {
        $id = SysUtils::decodeStr($codedId);
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $Class = get_called_class();
            return (new $Class)::find($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    final public function getCodedIdAttribute(int $id=null): ?string
    {
        $idValue = $id ?? $this->id;
        if (is_null($idValue)) {
            return null;
        }
        return SysUtils::encodeStr($idValue);
    }
}