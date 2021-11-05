<?php
namespace App\Services;

use App\Models\Mall;
use App\Facades\Cafe24\Cafe24Api;
use App\Facades\Cafe24\Cafe24;

class ProductService {

   /**
     * Get Product Detail
     *
     * @param array $params
     * @param string $access_token
     * @param string $cafe_mall_id
     *
     * @return array $result
     */
   public function getProductDetail($params) {
      $result = [
         "success" => false,
         "data" => [],
         "msg" => "",
      ];

      $cafe_mall_id = $params['cafe_mall_id'];
      $product_id = $params['product_id'];
      $shop_no = $params['shop_no'];

      //Validate params
      foreach ($params as $key => $value) {
         if (empty($value)) {
            $result['msg'] = "{$key} is mandatory";
            return $result;
         }
      }


      $endpoint_shops = "products/{$product_id}";
      $cf_params = [
         "shop_no" => $shop_no,
      ];
      $cafe24_token = Cafe24::getCafe24Token();
      $access_token = $cafe24_token['access_token'];
      $api_res = Cafe24Api::get($cafe_mall_id, $access_token, $endpoint_shops, $cf_params);
      $res_data = $api_res['data'];
      if (!empty($res_data)) {
         $product = $res_data->product;
         $result_data = [
            "product_no" => $product->product_no,
            "shop_no" => $shop_no,
            "description" => $product->description,
            "mobile_description" => $product->mobile_description
         ];
         $result['success'] = true;
         $result['data'] = $result_data;
         $result['msg'] = "Success";
      } else {
         $result['msg'] = "No data found!";
      }
      return $result;
   }
}