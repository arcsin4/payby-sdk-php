## 简介
这是一个为payby支付写的php的sdk

## 使用

### 创建订单
```php

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'lib/PayPageBuilder.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'PbPayPage.php';

require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';

function placeOrder($config, $orderno)
{

    $builder = new PayPageBuilder($config);

    $builder->setRequestApi('placeOrder');

    $builder->setRequestTime(time()*1000);

    $bz = array(
        'merchantOrderNo'=>$orderno,
        'subject'=>'test-subject',
        'totalAmount'=>array(
            "currency"=> "AED",
            "amount"=> 0.12
        ),
        'expiredTime'=>(time()+600)*1000,
        'payeeMid'=>$config['partner_id'],
        'paySceneCode'=>'PAYPAGE',

        'paySceneParams'=>array('redirectUrl'=>$config['redirect_url']),
        'notifyUrl'=>$config['notify_url'],
        #'accessoryContent'=>array("b"=>1,"a"=>2),
    );

    $builder->setBizContentarr($bz);

    $builder->generateSign();

    $payPage = new PbPayPage();

    $result=$payPage->request($builder);

    return $result;
}


$orderno = 'wp_1';

placeOrder($config, $orderno);

```


### 查询订单
```php

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'lib/PayPageBuilder.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'PbPayPage.php';

require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';

function getOrder($config, $orderno)
{

    $builder = new PayPageBuilder($config);

    $builder->setRequestApi('getOrder');

    $builder->setRequestTime(time()*1000);

    $bz = array(
        'merchantOrderNo'=>$orderno,
    );

    $builder->setBizContentarr($bz);

    $builder->generateSign();

    $payPage = new PbPayPage();

    $result=$payPage->request($builder);

    return $result;
}


$orderno = 'wp_1';

getOrder($config, $orderno);

```


### 取消订单
```php

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'lib/PayPageBuilder.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'PbPayPage.php';

require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';

function cancelOrder($config, $orderno)
{

    $builder = new PayPageBuilder($config);

    $builder->setRequestApi('cancelOrder');

    $builder->setRequestTime(time()*1000);

    $bz = array(
        'merchantOrderNo'=>$orderno,
    );

    $builder->setBizContentarr($bz);

    $builder->generateSign();

    $payPage = new PbPayPage();

    $result=$payPage->request($builder);

    return $result;

}

$orderno = 'wp_1';
cancelOrder($config, $orderno);

```


### 订单退款
```php

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'lib/PayPageBuilder.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'PbPayPage.php';

require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';

function refundOrder($config, $orderno, $refund_orderno)
{

    $builder = new PayPageBuilder($config);

    $builder->setRequestApi('refundOrder');

    $builder->setRequestTime(time()*1000);

    $bz = array(
        'refundMerchantOrderNo'=>$refund_orderno,
        'originMerchantOrderNo'=>$orderno,
        'amount'=>array(
            "currency"=> "AED",
            "amount"=> 0.12
        ),
        'notifyUrl'=>$config['notify_url'],
        'operatorName'=>'test',
        'reason'=>'test',

    );

    $builder->setBizContentarr($bz);

    $builder->generateSign();

    $payPage = new PbPayPage();

    $result=$payPage->request($builder);

    return $result;
}

$orderno = 'wp_1';
$refund_orderno = 'refund-'.$orderno;
refundOrder($config, $orderno, $refund_orderno);

```

### 查询退款订单
```php

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'lib/PayPageBuilder.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'PbPayPage.php';

require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';

function refundGetOrder($config, $orderno)
{

    $builder = new PayPageBuilder($config);

    $builder->setRequestApi('refundGetOrder');

    $builder->setRequestTime(time()*1000);

    $bz = array(
        'refundMerchantOrderNo'=>$orderno,
    );

    $builder->setBizContentarr($bz);

    $builder->generateSign();

    $payPage = new PbPayPage();

    $result=$payPage->request($builder);

    return $result;

}


$orderno = 'wp_1';
$refund_orderno = 'refund-'.$orderno;
refundGetOrder($config, $refund_orderno);

```