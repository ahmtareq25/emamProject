<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Utils\ApiResponseCode;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->all();
        $product = new Product();
        $productList = $product->getProducts($search);
        $variantList = (new Variant())->getVariants();

        return view('products.index', compact('productList','search', 'variantList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        $productObj = new Product();
        $status = $productObj->processProductStore($request->all());
        if ($status){
            return $this->sendApiResponse(ApiResponseCode::SUCCESS, 'Operation successful',[], ApiResponseCode::HTTP_CREATED);
        }
        return $this->sendApiResponse(ApiResponseCode::FAILED,'Operation Failed', [], ApiResponseCode::HTTP_BAD_REQUEST);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        $productVariants = ProductVariant::prepareProductVariant($product);
        $productVariantPrices = ProductVariantPrice::prepareProductVariantPriceDataByProduct($product);
        $productImages = ProductImage::prepareProductImages($product);
        return view('products.edit',
            compact('variants',
                'product',
                'productVariants',
                'productVariantPrices',
                'productImages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $productObj = new Product();
        $status = $productObj->processProductUpdate($request->all(), $product);
        if ($status){
            return $this->sendApiResponse(ApiResponseCode::SUCCESS, 'Product update successful',[], ApiResponseCode::HTTP_CREATED);
        }
        return $this->sendApiResponse(ApiResponseCode::FAILED,'Product update Failed', [], ApiResponseCode::HTTP_BAD_REQUEST);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function removeProductImage(Request $request){

    }
}
