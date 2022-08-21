<?php

namespace App\Traits;

trait ApiResponseTrait
{

    public function sendApiResponse($status_code, $status_description, $data, $http_code){
        $response = [
            'status_code' => $status_code,
            'status_description' => $status_description
        ];

        if ($http_code < 300) {
            $response['data'] = $data;
        }else{
            $response['errors'] = $data;
        }
        return response()->json($response, $http_code);
    }

}
