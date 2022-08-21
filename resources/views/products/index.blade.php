@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title"  value="{{$search['title'] ?? ''}}" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="" >--Select A Variant--</option>
                        @foreach($variantList as $variant)
                            <optgroup label="{{$variant->title}}">
                                @foreach($variant->productVariants->sortBy('variant_id') as $productVariant)
                                <option value="{{$productVariant->variant}}" {{(isset($search['variant']) && $search['variant'] == $productVariant->variant) ? 'selected' : ''}}>{{$productVariant->variant}}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control" value="{{$search['price_from'] ?? ''}}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control" value="{{$search['price_to'] ?? ''}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control" value="{{$search['date'] ?? ''}}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($productList as $product)
                    <tr>
                        <td>{{$product->id}}</td>
                        <td>{{$product->title}} <br> Created at : {{$product->created_at->format('d-M-Y')}}</td>
                        <td>{{substr($product->description, 0,25)}}..</td>
                        <td>

                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant{{$product->id}}">
                                @php
                                    $productVariantPrices = App\Models\ProductVariantPrice::prepareProductVariantPriceDataByProduct($product);
                                @endphp
                                @foreach($productVariantPrices as $item)
                                <dt class="col-sm-3 pb-0">
                                   {{$item['title']}}
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($item['price'],2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ $item['stock'] }}</dd>
                                    </dl>
                                </dd>
                                @endforeach
                            </dl>

                            <button onclick="$('#variant{{$product->id}}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>

                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $productList->firstItem() }} to {{ $productList->lastItem() }} of {{$productList->total()}}</p>
                </div>
                <div class="col-md-2">
                    {{ $productList->appends($search)->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
