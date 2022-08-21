<?php

namespace App\Models;

use App\Traits\SystemStorageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Product extends Model
{
    use SystemStorageTrait;
    protected $fillable = [
        'title', 'sku', 'description'
    ];


    public function variants(){
        return $this->belongsToMany(Variant::class, 'product_variants');
    }

    public function productVariants(){
        return $this->hasMany(ProductVariant::class)->orderBy('variant_id', 'ASC');
    }

    public function productVariantPrices(){
        return $this->hasMany(ProductVariantPrice::class);
    }

    public function productImages(){
        return $this->hasMany(ProductImage::class);
    }


    public function getProducts($search, $page_limit = 5){
        $query = self::query()
            ->with('productVariantPrices.productVariantOne')
            ->with('productVariantPrices.productVariantTwo')
            ->with('productVariantPrices.productVariantThree');

        if (!empty($search['title'])) {
            $query->where('title', 'LIKE', '%' . $search['title'] . '%');
        }

        if (!empty($search['variant'])) {
            $query->whereHas('productVariants', function ($query) use ($search) {
                $query->where('variant', $search['variant']);
            });
        }

        if (!empty($search['price_from']) && !empty($search['price_to'])) {
            $query->whereHas('productVariantPrices', function ($query) use ($search) {
                $query->where('price', '>=', $search['price_from'])
                    ->where('price', '<=', $search['price_to']);
            });
        } elseif (!empty($search['price_from'])){
            $query->whereHas('productVariantPrices', function ($query) use ($search) {
                $query->where('price', '>=',$search['price_from']);
            });
        }elseif (!empty($search['price_to'])){
            $query->whereHas('productVariantPrices', function ($query) use ($search) {
                $query->where('price', '<=',$search['price_to']);
            });
        }

        if (!empty($search['date'])){
            $query->whereDate('created_at', $search['date']);
        }


        return $query->orderBy('id', 'desc')->paginate($page_limit);
    }


    public function processProductStore($requestData){
        $status = false;
        try {
            DB::beginTransaction();

            $productData = [
                'title' => $requestData['title'],
                'sku' => $requestData['sku'],
                'description' => $requestData['description']
            ];
            $productObj = self::create($productData);
            $mappingProductVariantArr = $this->processProductVariant($requestData, $productObj->id);
            $this->processProductVariantPrice($requestData, $mappingProductVariantArr, $productObj->id);
            $this->processImageUpload($requestData, $productObj->id);
            DB::commit();
            $status = true;
        }catch (\Throwable $exception){
            DB::rollBack();
            info($exception->getMessage());
        }

        return $status;
    }

    private function getColumnName($number){
        $arr =[
            1 => 'product_variant_one',
            2 => 'product_variant_two',
            3 => 'product_variant_three'
        ];

        return $arr[$number];
    }

    private function processImageUpload($requestData, $product_id){
        if (!empty($requestData['product_image'])){

            $productImages = [];
            foreach ($requestData['product_image'] as $file){
                $this->moveFile($file, 'products/'.$product_id);
                $productImages[] = [
                    'product_id' => $product_id,
                    'file_path' => $this->db_directory,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            ProductImage::insert($productImages);
        }
    }

    private function processProductVariant($requestData,$product_id){
        $mappingProductVariantArr = [];

        foreach ($requestData['product_variant'] as $variantItem){

            foreach ($variantItem['tags'] as $tag){
                $productVariantData = [
                    'variant_id' => $variantItem['option'],
                    'product_id' => $product_id,
                    'variant' => $tag
                ];

                $productVariantObj = ProductVariant::create($productVariantData);

                $mappingProductVariantArr[$tag] = $productVariantObj->id;
            }
        }
        return $mappingProductVariantArr;
    }

    private function processProductVariantPrice($requestData, $mappingProductVariantArr, $product_id){
        $productVariancePriceDataArr = [];

        foreach ($requestData['product_variant_prices'] as $item){
            $variantArr = array_diff(array_map('trim', explode("/", $item['title'])),array(""));
            $productVariancePriceData = [
                'price'=> $item['price'],
                'stock'=> $item['stock'],
                'product_id' => $product_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $counter = 1;

            foreach ($variantArr as $aVariant){
                $productVariancePriceData[$this->getColumnName($counter)] = $mappingProductVariantArr[$aVariant];
                $counter++;
                if ($counter > 3){
                    break;
                }
            }

            $productVariancePriceDataArr[] = $productVariancePriceData;

        }

        ProductVariantPrice::insert($productVariancePriceDataArr);
    }

    public function processProductUpdate($requestData, $product){
        $status = false;
        try {
            DB::beginTransaction();
            $productData = [
                'title' => $requestData['title'],
                'sku' => $requestData['sku'],
                'description' => $requestData['description']
            ];
            $product->fill($productData);
            $product->save();

            $product->productVariants()->delete();
            $product->productVariantPrices()->delete();
            $mappingProductVariantArr = $this->processProductVariant($requestData, $product->id);
            $this->processProductVariantPrice($requestData, $mappingProductVariantArr, $product->id);
            $this->processImageUpload($requestData, $product->id);
            DB::commit();
            $status = true;
        }catch (\Throwable $exception){
            DB::rollBack();
            info($exception->getMessage());
        }
        return $status;
    }

}
