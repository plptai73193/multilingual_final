<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    /**
     * Get Categories List
     *
     * @param  mixed $request
     * @return $result
     */
    public function getCategoriesList(Request $request){
        $params = [
            "cafe_mall_id" => $request->cafe_mall_id,
            "shop_no" => $request->shop_no,
        ];
        $category_service = new CategoryService();
        $api_result = $category_service->getCategoriesList($params);
        return $api_result;
    }
}
