<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\ProductService;

class ProductController extends Controller
{
    /**
     * Get Product Detail
     *
     * @param  mixed $request
     * @return void
     */
    public function getProductDetail(Request $request){
        $params = [
            "cafe_mall_id" => $request->cafe_mall_id,
            "product_id" => $request->product_id,
            "shop_no" => $request->shop_no,
        ];
        $product_service = new ProductService();
        $api_result = $product_service->getProductDetail($params);
        return $api_result;
    }

    
    /**
     * Get Product Detail
     *
     * @param  mixed $request
     * @return $result
     */
    public function getProductList(Request $request){
        $params = [
            "cafe_mall_id" => $request->cafe_mall_id,
            "product_no" => $request->product_no,
            "shop_no" => $request->shop_no,
        ];
        $product_service = new ProductService();
        $api_result = $product_service->getProductList($params);
        return $api_result;
    }
}
