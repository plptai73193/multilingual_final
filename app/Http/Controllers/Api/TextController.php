<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Mall;


class TextController extends Controller{



    public function index(Request $request){
        $uuids = $request->row_id;
        $cafe24_mall_id = $request->cafe24_mall_id;
        $pages_name = $request->page_name;
        $pages_url = $request->page_url;
        // dd($pages_url);
        $selectors = $request->selector;
        $languages = $request->language;
        $input_texts = $request->input_text;
        $is_placeholders = $request->is_placeholder;
        $text_param = [];
        $i = -1;

        $result = [
            "success" => false,
            "data" => [],
            "msg" => "",
        ];



        foreach($pages_name as $key => $value){
            $i++;
            $text_param_temp = [
                "page_name" => $value,
                "page_url" => $pages_url[$i],
                "uuid" => $uuids[$i],
                "selector" => $selectors[$i],
                "language" => $languages[$i],
                "input_text" => $input_texts[$i],
                "is_placeholder" => $is_placeholders[$i],
            ];
            array_push($text_param, $text_param_temp);
        }
        if(!empty($text_param)){
            foreach($text_param as $key => $value){
                $malls = DB::table('translated_texts')->where([
                    'cafe24_mall_id' => $cafe24_mall_id,
                    'uuid' => $value['uuid'],
                    'is_deleted' => '0',
                ])->get()->toArray();
                if(empty($malls)){
                    $uuid = Str::uuid()->toString();
                    DB::table('translated_texts')->insert([
                        [
                            'uuid' => $uuid,
                            'cafe24_mall_id' => $cafe24_mall_id,
                            'page_name' => $value['page_name'],
                            'page_url' => $value['page_url'],
                            'selector' => $value['selector'],
                            'language' => $value['language'],
                            'input_text' => $value['input_text'],
                            'is_placeholder' => $value['is_placeholder'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ],
                    ]);
                    DB::table('edit_text_logs')->insert([
                        [
                            'cafe24_mall_id' => $cafe24_mall_id,
                            'status' => 'create',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ],
                    ]);
                    $result["success"] = true;
                    $result["msg"] = '200';
                    array_push($result["data"],$uuid);
                } else {
                    DB::table('translated_texts')->where([
                        'cafe24_mall_id' => $cafe24_mall_id,
                        'uuid' => $value['uuid'],
                    ])->update([
                        'page_name' => $value['page_name'],
                        'page_url' => $value['page_url'],
                        'selector' => $value['selector'],
                        'language' => $value['language'],
                        'input_text' => $value['input_text'],
                        'is_placeholder' => $value['is_placeholder'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    DB::table('edit_text_logs')->insert([
                        [
                            'cafe24_mall_id' => $cafe24_mall_id,
                            'status' => 'edit',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ],
                    ]);
                    array_push($result["data"],$value['uuid']);
                    $result["success"] = true;
                    $result["msg"] = '200';
                }
            }
            return $result;
        } else {
            $result["msg"] = '404';
            return $result;
        }
    }





    public function delete(Request $request){
        $result = [
            "success" => false,
            "data" => [],
            "msg" => "",
        ];
        $row_id = $request->row_id;
        $database_row_id = DB::table('translated_texts')->where([
                    'uuid' => $row_id,
                ])->get()->toArray();

        if(!empty($database_row_id)){
            DB::table('translated_texts')->where([
                'uuid' => $row_id,
            ])->update([
                'is_deleted' => '1',
            ]);
            $result["success"] = true;
            $result["msg"] = '200';
            return $result;
        } else {
            $result["msg"] = '404';
            return $result;
        }
    }





    public function translatetext (Request $request){
        $cafe24_mall_id = $request->cafe24_mall_id;
        $page_url = $request->page_url;
        $lang = $request->lang;
        $result = [
            "success" => false,
            "data" => [],
            "msg" => "",
        ];
        $all_translate_text = DB::table('translated_texts')->where([
                    'cafe24_mall_id' => $cafe24_mall_id,
                    'page_url' => $page_url,
                    'language' => $lang,
                    'is_deleted' => '0',
                ])->get()->toArray();
        if(!empty($all_translate_text)){
            $data = [];
            foreach ($all_translate_text as $key => $translate_text) {
                $temp = [
                    'page_url' => $translate_text->page_url,
                    'selector' => $translate_text->selector,
                    'text' => $translate_text->input_text,
                    'is_placeholder' => $translate_text->is_placeholder,
                ];
                array_push($data,$temp);
            }
            DB::table('api_logs')->insert([
                [
                    'cafe24_mall_id' => $cafe24_mall_id,
                    'status' => 'success',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ]);
            $result["success"] = true;
            $result["msg"] = '200';
            $result['data'] = $data;
        } else {
            DB::table('api_logs')->insert([
                [
                    'cafe24_mall_id' => $cafe24_mall_id,
                    'status' => 'Not found',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ]);
            $result["success"] = false;
            $result["msg"] = '404';
        }
        return $result;
    }




    public function getAppTable (Request $request){
        $cafe24_mall_id = $request->cafe24_mall_id;
        $offset = $request->page;
        $mall_texts = [];

        $result = [
            "success" => false,
            "data" => [],
            "msg" => "",
        ];


        $texts = DB::table('translated_texts')->where([
                           'cafe24_mall_id' => $cafe24_mall_id,
                           'is_deleted' => '0',
                        ])->paginate(20)->toArray();
        if(!empty($texts)){

            $text_datas = $texts['data'];
            $total = $texts['total'];
            $current_page =$texts['current_page'];
            $first_page_url =$texts['first_page_url'];
            $last_page_url =$texts['last_page_url'];
            $next_page_url =$texts['next_page_url'];
            $prev_page_url =$texts['prev_page_url'];
            $last_page =$texts['last_page'];
            $mall_langs = [];

            


            $installed_malls = Mall::where('cafe_mall_id', $cafe24_mall_id)->get()->toArray();
            foreach ($installed_malls as $installed_mall) {
                $temp_mall_langs = [
                    "shop_name" => $installed_mall['mall_name'],
                    "lang_code" => $installed_mall['language']
                ];
                array_push($mall_langs, $temp_mall_langs);
            }
            if(!empty($text_datas)){
                foreach ($text_datas as $text) {
                    $text_temp = [
                        'row_id' => $text->uuid,
                        'page_url' => $text->page_url,
                        'page_name' => $text->page_name,
                        'page_name' => $text->page_name,
                        'selector' => $text->selector,
                        'language' => $text->language,
                        'input_text' => $text->input_text,
                        'is_placeholder' => $text->is_placeholder,
                    ];
                    array_push($mall_texts, $text_temp);
                }
                $result["success"] = true;
                $result["data"] = [
                    "text_data" => $mall_texts,
                    "total" => $total,
                    "current_page" => $current_page,
                    "first_page_url" => $first_page_url,
                    "last_page_url" => $last_page_url,
                    "next_page_url" => $next_page_url,
                    "prev_page_url" => $prev_page_url,
                    "last_page" => $last_page,
                    "mall_langs" => $mall_langs,
                ];
                $result["msg"] = "200";
            } else {
                $result["success"] = true;
                $result["data"] = $text_datas;
                $result["msg"] = "No data found";
            }
            
        } else {
            $result["success"] = false;
        }
        
        return $result;
    }



    public function search (Request $request){
        $search_criteria = $request->criteria;
        $search_input = $request->keyword;
        $cafe24_mall_id = $request->cafe24_mall_id;
        $mall_texts = [];
        $mall_langs = [];
            


            


        $result = [
            "success" => false,
            "data" => [],
            "msg" => "",
        ];




        
        $texts = DB::table('translated_texts')->where([
                           $search_criteria => $search_input,
                           'is_deleted' => '0',
                        ])->get()->toArray();

        $installed_malls = Mall::where('cafe_mall_id', $cafe24_mall_id)->get()->toArray();
        foreach ($installed_malls as $installed_mall) {
            $temp_mall_langs = [
                "shop_name" => $installed_mall['mall_name'],
                "lang_code" => $installed_mall['language']
            ];
            array_push($mall_langs, $temp_mall_langs);
        }     
        
        
        if(!empty($texts)){
            foreach ($texts as $text) {
                $text_temp = [
                    'row_id' => $text->uuid,
                    'page_url' => $text->page_url,
                    'page_name' => $text->page_name,
                    'page_name' => $text->page_name,
                    'selector' => $text->selector,
                    'language' => $text->language,
                    'input_text' => $text->input_text,
                    'is_placeholder' => $text->is_placeholder,
                ];
                array_push($mall_texts, $text_temp);
            }
            $result["success"] = "true";
            $result["data"] = [
                "translated_texts" => $mall_texts,
                "mall_langs" => $mall_langs
            ];
            $result["msg"] = "200";
        }
        return $result;
    }

}
