<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\Country;

class ApiCountry extends Controller
{
    public function provincesByCountry(Request $request)
    {
        $rData = $request->only(['country']);
        
        return $this->returnResponse(
            false,
            'Estados retornados com sucesso!',
            [
                'provinces' => Country::getProvinceByCountry($rData['country'] ?? '')
            ],
            Response::HTTP_OK
        );
    }
}