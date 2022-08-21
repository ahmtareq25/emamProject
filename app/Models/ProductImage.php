<?php

namespace App\Models;

use App\Traits\SystemStorageTrait;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use SystemStorageTrait;
    protected $guarded = [];

    public static function prepareProductImages($product){
        $collection = collect();
        $obj = new self();
        foreach ($product->productImages as $productImage){
            $productImage->attributes['full_url'] = $obj->getFullUrl($productImage->file_path);
            $productImage->original['full_url'] = $obj->getFullUrl($productImage->file_path);

            $collection->add($productImage);
        }
        return $collection;
    }

    public function findById($id){
        return $this->find($id);
    }

    public function deleteById($id){
        self::destroy($id);
    }

    public function processDelete($id){
        $status = false;
        $productImageObj = $this->findById($id);
        if (!empty($productImageObj)){
            $this->removeFile($productImageObj->file_path);
            $this->deleteById($productImageObj->id);
            $status = true;
        }
        return $status;
    }
}
