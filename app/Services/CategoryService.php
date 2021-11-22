<?php
namespace App\Services;

use App\Models\Mall;
use App\Facades\Cafe24\Cafe24Api;
use App\Facades\Cafe24\Cafe24;

class CategoryService {
   public function __construct(){
      $this->cafe_24_token = Cafe24::getCafe24Token();
      $this->access_token = $this->cafe_24_token['access_token'];
   }



   /**
     * Get Categories List
     *
     * @param array $params
     *
     * @return array $result
     */
   public function getCategoriesList($params) {
      $result = [
         "success" => false,
         "data" => [],
         "msg" => "",
      ];
      $cafe_mall_id = $params['cafe_mall_id'];
      $shop_no = $params['shop_no'];

      //Validate params
      foreach ($params as $key => $value) {
         if (empty($value)) {
            $result['msg'] = "{$key} is mandatory";
            return $result;
         }
      }

      $endpoint = "categories";
      $cf_params = [
         "shop_no" => $shop_no,
         "limit" => 100
      ];
      $cafe24_token = Cafe24::getCafe24Token();
      $access_token = $cafe24_token['access_token'];
      $api_res = Cafe24Api::get($cafe_mall_id, $access_token, $endpoint, $cf_params);
      $res_data = $api_res['data']->categories;
      if (!empty($res_data)) {
         $categories = $res_data;
         $temp_data["shop_no"] = $shop_no;
         foreach ($categories as $cate) {
            $temp_data["categories"][] = [
               "category_no" => $cate->category_no,
               "category_depth" => $cate->category_depth,
               "parent_category_no" => $cate->parent_category_no,
               "category_name" => $cate->category_name,
               "full_category_name" => $cate->full_category_name,
               "full_category_no" => $cate->full_category_no,
               "root_category_no" => $cate->root_category_no,
            ];
         }
         $result['success'] = true;
         $result['data'] = $temp_data;
         $result['msg'] = "Success";
      } else {
         $result['msg'] = $api_res["msg"];
      }
      return $result;
   }
}