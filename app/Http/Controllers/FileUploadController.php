<?php

namespace App\Http\Controllers;

use App\Traits\SystemStorageTrait;
use App\Utils\ApiResponseCode;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    use SystemStorageTrait;

    public function upload(Request $request){

        if (!empty($request->file())){
            $file = $request->file(array_key_first($request->file()));

            $this->uploadFile($file, 'tmp');//By scheduler, we can delete tmp folders files.
            $data = [
                'file_url' => $this->full_url,
                'path'=> $this->db_directory
            ];

            return $this->sendApiResponse(ApiResponseCode::SUCCESS,'Upload Successful', $data, ApiResponseCode::HTTP_SUCCESS);

        }

        return $this->sendApiResponse(ApiResponseCode::FAILED,'Upload Failed', [], ApiResponseCode::HTTP_BAD_REQUEST);
    }

}
