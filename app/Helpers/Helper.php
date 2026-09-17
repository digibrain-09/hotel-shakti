<?php


function getEncrypted($id){
    $encrypted_string=openssl_encrypt($id,config('services.encryption.type'),config('services.encryption.secret'));
    return base64_encode($encrypted_string);
}
function getDecrypted($id){
    $string=openssl_decrypt(base64_decode($id),config('services.encryption.type'),config('services.encryption.secret'));
    return $string;
}

function getActiveClass($routes = [])
{
    $class = '';
    if(in_array(\Route::currentRouteName(), $routes)){
        $class = "active";
    }
    return $class;
}

function local_upload_path($path){
    $upload = public_path('uploads').'/'.$path;
    return $upload;
}
function customOrderBy($column_name = "", $orderby_val = "", $request_column = "", $column_title = "", $default_column = "", $default_val = "", $default_class = "")
{
    if($column_title == ""){
        $column_title = ucwords(str_replace('_', ' ', $column_name));
    }
    if(($request_column == $column_name) || $default_column != ""){
        if($default_val != ""){
            $orderby_val = $orderby_val;
        }
        if($orderby_val == 'asc'){
            $cust_column = '<th class="orderby sorting sorting_asc '.$default_class.' " data-column="'.$column_name.'" data-orderby="'. $orderby_val .'">'. $column_title .'</th>';
        }else{
            $cust_column = '<th class="orderby sorting sorting_desc '.$default_class.'" data-column="'.$column_name.'" data-orderby="'. $orderby_val .'">'. $column_title .'</th>';
        }
    }else{
        $cust_column = '<th class="orderby sorting '.$default_class.' " data-column="'.$column_name.'" data-orderby="asc">'. $column_title .'</th>';
    }
    return $cust_column;
}


function getEmbeddedURL($url){
    if(!strstr($url,'embed')){
        $parse = parse_url($url);
        $url = $parse['scheme']."://". $parse['host'].'/embed'.$parse['path'];
    }
    return $url;

}