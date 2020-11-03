<?php
$config = array (

    //Content Language, default: en
    'content_language' => "en",

    //商户编号
    'partner_id' => "",

    //商户私钥文件路径，您的原始格式RSA私钥，生成签名使用
    'merchant_private_key_path' => "",
    //商户私钥文件内容，您的原始格式RSA私钥，生成签名使用
    'merchant_private_key' => "",

    //异步通知地址
    'notify_url' => "",#"http://www/notify_url.php",

    //同步跳转
    'redirect_url' => "",#"http://www/redirect_url.php",

    //编码格式
    //'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //网关切换
    'gateway_host' => "https://uat.test2pay.com", // Staging
    //'gateway_host' => "https://api.payby.com", // Production

    //payby公钥文件路径，验签使用
    'payby_public_key_path' => "",
    //payby公钥文件内容，验签使用
    #'payby_public_key' => "",

    'log_switch'=> true,

    'gateway_path'=>array(
        'placeOrder' => "/sgs/api/acquire2/placeOrder",
        'cancelOrder' => "/sgs/api/acquire2/cancelOrder",
        'getOrder' => "/sgs/api/acquire2/getOrder",

        'refundOrder' => "/sgs/api/acquire2/refund/placeOrder",
        'refundGetOrder' => "/sgs/api/acquire2/refund/getOrder",
    ),
);
