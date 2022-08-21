<?php

namespace App\Http\Requests;

use App\Traits\ApiResponseTrait;
use App\Utils\ApiResponseCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
{
    use ApiResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules= [
            'title' => 'required|max:255',
            'sku' => 'required|unique:products|max:255',
            'description' => 'required|max:1000',
            'product_variant' => 'required',
            'product_variant_prices' => 'required',
            'product_image' => 'sometimes'
        ];
        if ($this->method() == 'PUT'){
            $rules['sku'] = 'required|max:255|unique:products,sku,'.$this->route('product.id');
        }
        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->isJson() || $this->wantsJson()){

            throw new HttpResponseException( $this->sendApiResponse(
                ApiResponseCode::FAILED,
                'Validation Error!',
                $validator->errors(),
                ApiResponseCode::HTTP_VALIDATION_ERROR
            ));


        }else{

            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }

    }
}
