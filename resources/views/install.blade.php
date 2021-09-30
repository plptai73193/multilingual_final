<?php
use App\Libs\Cafe24\Cafe24Token;
use \Illuminate\Support\Facades\DB;
use App\Services\Cafe24\Cafe24Api;
use App\Models\Mall;



$client_id = (env('APP_ENV') == "production") ? env('CAFE24_APP_CLIENT_ID') : env('CAFE24_APP_CLIENT_ID_DEV');
$client_secret = (env('APP_ENV') == "production") ? env('CAFE24_APP_CLIENT_SECRET') : env('CAFE24_APP_CLIENT_SECRET_DEV');
$redirect_uri = (env('APP_ENV') == "production") ? env('CAFE24_APP_REDIRECT_URI') : env('CAFE24_APP_REDIRECT_URI_DEV');
$app_uri = (env('APP_ENV') == "production") ? env('CAFE24_APP_APP_URI') : env('CAFE24_APP_APP_URI_DEV');
$app_code = env("CAFE24_APP_CODE");

/******** App Setting ********/
$app_setting = [
    "mall_id"               =>      @$_REQUEST["mall_id"] ? $_REQUEST["mall_id"] : "",
    "state"                 =>      env("CAFE24_APP_STATE"),
    "client_id"             =>      $client_id,
    "client_secret"         =>      $client_secret,
    "redirect_uri"          =>      $redirect_uri,
    "scope"                 =>      env("CAFE24_APP_SCOPE"),
    "code"                  =>      @$_GET["code"] ? @$_GET["code"] : $app_code,
];
/******** App Setting ********/

$error = Cafe24Token::getError($_GET);
if (!empty($error)) {
    echo $error;
    return false;
}
// dd($app_setting);


/* First Setup App */
Cafe24Token::installApp($app_setting);

/* Get Installed Mall Id App */
$http_request = $_SERVER["HTTP_REFERER"];
$mall_id_installed = Cafe24Token::getInstalledMallId($http_request);
$app_setting["mall_id"] = $mall_id_installed;


/* Get token */
$tokenData = Cafe24Token::getToken($app_setting);
$code = @$_GET["code"] ? @$_GET["code"] : $app_code;

if (!empty($tokenData->error)) {
    $error = $tokenData->error;
    $error_description = $tokenData->error_description;
    $error_msg = "[{$error}] {$error_description}";
    echo $error_msg;
} else {
    $access_token = $tokenData->access_token;
    $refresh_token = $tokenData->refresh_token;
    $client_id = $tokenData->client_id;
    $cafe_mall_id = $tokenData->mall_id;
    $scopes = $tokenData->scopes;

    /* Validate mall info and insert Source Mall */
    if (!empty($cafe_mall_id) && !empty($access_token) && !empty($refresh_token)) {
        $shop = null;
        $debug = null;

        $app_url = (env('APP_ENV') == "production") ? env('APP_URL') : env('APP_URL_DEV');
        
        $mall_param = [
            "cafe_mall_id" => $cafe_mall_id,
            "access_token" => $access_token,
            "refresh_token" => $refresh_token,
        ];
        
        
        /* Create new Mall if not existed */
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$app_url}/api/v1/mall/store",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $mall_param,
        ));
        $response_curl = curl_exec($curl);
        $err_curl = curl_error($curl);
        curl_close($curl);



        if ($err_curl) {
            echo 'cURL Error #:' . $err_curl;
        } else {
            $debug = $response_curl;
            $response_curl = json_decode($response_curl, true);

            if ($response_curl["success"] == false) {
                echo $response_curl["msg"];
            } else {
                $msg = $response_curl["msg"];
                $data = $response_curl["data"];
                // $shop = $data["mall"];
                $logs = $data["logs"];
                $installed_malls = Mall::where('cafe_mall_id', $cafe_mall_id)->get()->toArray();
                $mall_langs = [];
                foreach ($installed_malls as $installed_mall) {
                    $temp_mall_langs = [
                        "shop_name" => $installed_mall['mall_name'],
                        "lang_code" => $installed_mall['language']
                    ];
                    array_push($mall_langs, $temp_mall_langs);
                }
                
                ?>
        
                <?php if ($response_curl["success"] === false) {
                    $error_msg = $msg;
                    echo $error_msg;
                } else {
                    // $cafe_mall_id = $shop["cafe_mall_id"];
                    // $mall_id = $shop["id"];
                    // $mall_langs = $shop["language"];
                    $mall_params = [
                        'mall_id' => $mall_id_installed,
                        'mall_langs' => $mall_langs
                    ];
                    $mall_params = json_encode($mall_params);
                    $mall_params = base64_encode($mall_params);
                    
                    header("Location:{$app_uri}/{$mall_params}");
                    //redirect()->route('app', $mall_params);
                }
            }
            // var_dump($debug);
        }
        /* Create new Mall if not existed:E */
        ?>

    <?php }
}
?>