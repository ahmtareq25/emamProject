<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $guarded = [];

    public function productVariantOne(){
        return $this->hasOne(ProductVariant::class,  'id', 'product_variant_one');
    }

    public function productVariantTwo(){
        return $this->hasOne(ProductVariant::class,'id', 'product_variant_two');
    }

    public function productVariantThree(){
        return $this->hasOne(ProductVariant::class,'id', 'product_variant_three');
    }

    public static function prepareProductVariantPriceDataByProduct($product){
        $variants = [];
        $productVariantPriceData = [];
        foreach ($product->productVariantPrices as $productPrice){
            if(!empty($productPrice->productVariantOne)){
                $variants[$productPrice->productVariantOne->variant_id] = $productPrice->productVariantOne->variant;
            }
            if(!empty($productPrice->productVariantTwo)){
                $variants[$productPrice->productVariantTwo->variant_id] = $productPrice->productVariantTwo->variant;
            }
            if(!empty($productPrice->productVariantThree)){
                $variants[$productPrice->productVariantThree->variant_id] = $productPrice->productVariantThree->variant;
            }
            ksort($variants);

            $productVariantPriceData[] = [
                'title' => implode('/ ', $variants),
                'price' => $productPrice->price,
                'stock' =>  $productPrice->stock
            ];
        }
        return collect($productVariantPriceData);

    }
}
