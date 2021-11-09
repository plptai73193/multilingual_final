<?php
namespace App\Services;

use App\Models\Mall;
use App\Facades\Cafe24\Cafe24Api;
use App\Facades\Cafe24\Cafe24;

class ProductService {


   public function __construct(){
      $this->cafe_24_token = Cafe24::getCafe24Token();
      $this->access_token = $this->cafe_24_token['access_token'];
   }


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


      $endpoint = "products/{$product_id}";
      $cf_params = [
         "shop_no" => $shop_no,
      ];
      $cafe24_token = Cafe24::getCafe24Token();
      $access_token = $cafe24_token['access_token'];
      $api_res = Cafe24Api::get($cafe_mall_id, $access_token, $endpoint, $cf_params);
      $res_data = $api_res['data'];
      if (!empty($res_data)) {
         $product = $res_data->product;
         $result_data = [
            "shop_no" => $shop_no,
            "product_no" => $product->product_no,
            "product_name" => $product->product_name, 
            "simple_description" => $product->simple_description, 
            "description" => $product->description,
            "mobile_description" => $product->mobile_description,
            "translated_description" => $product->translated_description,
            "product_tag" => $product->product_tag,
            "internal_product_name" => $product->internal_product_name,
            "model_name" => $product->model_name,
            "supply_price" => $product->supply_price,
            "translated_additional_description" => $product->translated_additional_description,
         ];
         $result['success'] = true;
         $result['data'] = $result_data;
         $result['msg'] = "Success";
      } else {
         $result['msg'] = $api_res["msg"];
      }
      return $result;
   }



   
   /**
    * getProductList
    *
    * @param  array $params
    * @return $result
    */
   public function getProductList($params) {
      $result = [
         "success" => false,
         "data" => [],
         "msg" => "",
      ];

      //Validate params
      foreach ($params as $key => $value) {
         if (empty($value)) {
            $result['msg'] = "{$key} is mandatory";
            return $result;
         }
      }

      $cafe_mall_id = $params['cafe_mall_id'];
      $products = $params['products'];
      $shop_no = $params['shop_no'];
      $endpoint = "products";
      $cf_params = [
         "shop_no" => $shop_no,
         "product_no" => $products,
         "display" => "T"
      ];
      $access_token = $this->access_token; 
      $api_res = Cafe24Api::get($cafe_mall_id, $access_token, $endpoint, $cf_params);
      if($api_res['success'] == true){
         $result['success'] = true;
         $res_data = $api_res['data'];
         $products = $res_data->products;
         if (!empty($products)) {
            foreach ($products as $product) {
               $result_data["products"][] = [
                  "shop_no" => $shop_no,
                  "product_no" => $product->product_no,
                  "product_code" => $product->product_code,
                  "list_image" => $product->list_image,
                  "product_name" => $product->product_name,
                  "price" => $product->price,
               ];
            }
            $result['data'] = $result_data;
            $result['msg'] = "Success";
         } else {
            $result['msg'] = "No data found!";
         }
      }
      return $result;
   }
}