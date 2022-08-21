<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{

    protected $guarded = [];

    public static function prepareProductVariant($product){
        $productVariantsData = [];
        foreach ($product->productVariants as $productVariant){
            $productVariantsData[$productVariant->variant_id][] = $productVariant->variant;
        }
        $productVariantsDataFormat = [];
        foreach ($productVariantsData as $key => $tags){
            $productVariantsDataFormat[] = [
                'option' => $key,
                'tags' => $tags
            ];
        }
        return collect($productVariantsDataFormat);
    }
}
