<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use App\Traits\SystemStorageTrait;
use App\Utils\ApiResponseCode;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    use SystemStorageTrait;


    public function remove(Request $request, $id){

        $productImage = new ProductImage();
        $status = $productImage->processDelete($id);
        if ($status){
            return $this->sendApiResponse(ApiResponseCode::SUCCESS, 'Product Image remove successful',[], ApiResponseCode::HTTP_ACCEPTED);
        }
        return $this->sendApiResponse(ApiResponseCode::FAILED,'Product Image remove Failed', [], ApiResponseCode::HTTP_FORBIDDEN);


    }
}
