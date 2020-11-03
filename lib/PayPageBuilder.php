<?php

class PayPageBuilder
{
    private $apiName = "";
    private $gatewayPathArr = array();

    //Base Config Start
    private $gatewayHost = "";
    private $gatewayPath = "";

    private $merchantPrivateKeyPath = "";
    private $merchantPrivateKey = "";
    private $paybyPublicKeyPath = "";
    private $paybyPublicKey = "";

    private $signType = "RSA2";

    private $logSwitch = true;

    //Base Config End

    private $headerArr = array();

    //Http Header Start
    private $contentType = "application/json";
    private $contentLanguage;
    private $signContent;
    private $partnerId;
    //Http Header End

    //Request time
    private $requestTime;

    //Business content
    private $bizContentarr = array();

    public function __construct($payby_config)
    {
        $this->headerArr['Content-Type'] = $this->contentType;

        if(isset($payby_config['gateway_host'])){
            $this->setGatewayHost($payby_config['gateway_host']);
        }

        if(isset($payby_config['merchant_private_key_path'])){
            $this->setMerchantPrivateKeyPath($payby_config['merchant_private_key_path']);
        }

        if(isset($payby_config['merchant_private_key'])){
            $this->setMerchantPrivateKey($payby_config['merchant_private_key']);
        }

        if(isset($payby_config['payby_public_key_path'])){
            $this->setPaybyPublicKeyPath($payby_config['payby_public_key_path']);
        }

        if(isset($payby_config['payby_public_key'])){
            $this->setPaybyPublicKey($payby_config['payby_public_key']);
        }

        if(isset($payby_config['sign_type'])){
            $this->setSignType($payby_config['sign_type']);
        }

        if(isset($payby_config['content_language'])){
            $this->setContentLanguage($payby_config['content_language']);
        }

        if(isset($payby_config['partner_id'])){
            $this->setPartnerId($payby_config['partner_id']);
        }

        if(isset($payby_config['log_switch'])){
            $this->setLogSwitch($payby_config['log_switch']);
        }

        if(isset($payby_config['gateway_path'])){
            $this->gatewayPathArr = $payby_config['gateway_path'];
        }
    }

    public function getRequestApi()
    {
        return $this->apiName;
    }


    public function setRequestApi($api_name)
    {
        if(array_key_exists($api_name, $this->gatewayPathArr))
        {
            $this->apiName = $api_name;

            $this->setGatewayPath($this->gatewayPathArr[$api_name]);
        }
    }


    public function getGatewayUrl(){
        return $this->gatewayHost.$this->gatewayPath;
    }

    public function getHeaderArr()
    {
        return $this->headerArr;
    }

    public function getDataContent(){

        return json_encode($this->getData());
    }

    public function getData(){
        $bc = $this->bizContentarr;
        ksort($bc);

        $data = array('requestTime'=>$this->getRequestTime(),'bizContent'=>$bc);
        ksort($data);
        return $data;
    }

    public function getParams(){
        return array();
    }

    public function setBizContentarr($bizContentarr)
    {
        $this->bizContentarr = $bizContentarr;
    }

    public function getRequestTime()
    {
        return $this->requestTime;
    }

    public function setRequestTime($requestTime)
    {
        $this->requestTime = $requestTime;
    }

    public function getGatewayHost()
    {
        return $this->gatewayHost;
    }

    public function setGatewayHost($gatewayHost)
    {
        $this->gatewayHost = $gatewayHost;
    }

    public function getGatewayPath()
    {
        return $this->gatewayPath;
    }

    public function setGatewayPath($gatewayPath)
    {
        $this->gatewayPath = $gatewayPath;
    }

    public function getMerchantPrivateKeyPath()
    {
        return $this->merchantPrivateKeyPath;
    }

    public function setMerchantPrivateKeyPath($merchantPrivateKeyPath)
    {
        $this->merchantPrivateKeyPath = $merchantPrivateKeyPath;
    }

    public function getMerchantPrivateKey()
    {
        return $this->merchantPrivateKey;
    }

    public function setMerchantPrivateKey($merchantPrivateKey)
    {
        $this->merchantPrivateKey = $merchantPrivateKey;
    }


    public function getPaybyPublicKeyPath()
    {
        return $this->paybyPublicKeyPath;
    }

    public function setPaybyPublicKeyPath($paybyPublicKeyPath)
    {
        $this->paybyPublicKeyPath = $paybyPublicKeyPath;
    }

    public function getPaybyPublicKey()
    {
        return $this->paybyPublicKey;
    }

    public function setPaybyPublicKey($paybyPublicKey)
    {
        $this->paybyPublicKey = $paybyPublicKey;
    }

    public function getSignType()
    {
        return $this->signType;
    }

    public function setSignType($signType)
    {
        $this->signType = $signType;
    }

    public function getLogSwitch()
    {
        return $this->logSwitch;
    }

    public function setLogSwitch($logSwitch)
    {
        $this->logSwitch = $logSwitch;
    }

    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;
        $this->headerArr['Content-Language'] = $contentLanguage;
    }

    public function getPartnerId()
    {
        return $this->partnerId;
    }

    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        $this->headerArr['Partner-Id'] = $partnerId;
    }


    public function getSignContent()
    {
        return $this->signContent;
    }

    public function generateSign() {
        $this->signContent = $this->sign($this->getDataContent());
        $this->headerArr['sign'] = $this->signContent;
    }

    public function sign($data) {
        if($this->checkEmpty($this->merchantPrivateKeyPath)){
            $res = $this->merchantPrivateKey;
        }else {
            $priKey = file_get_contents($this->merchantPrivateKeyPath);
            $res = openssl_get_privatekey($priKey);
        }

        #($res) or die('The private key you used is in the wrong format, please check the RSA private key configuration');

        if ("RSA2" == $this->signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        if(!$this->checkEmpty($this->merchantPrivateKeyPath)){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    public function verify($data, $sign)
    {

        if ($this->checkEmpty($this->paybyPublicKeyPath)) {

            $res = $this->paybyPublicKey;
        } else {
            //读取公钥文件
            $pubKey = file_get_contents($this->paybyPublicKeyPath);
            //转换为openssl格式密钥
            $res = openssl_get_publickey($pubKey);
        }

        #($res) or die('Payby RSA public key is wrong. Please check if the public key file format is correct');

        //调用openssl内置方法验签，返回bool值

        $result = FALSE;
        if ("RSA2" == $this->signType) {
            $result = (openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256) === 1);
        } else {
            $result = (openssl_verify($data, base64_decode($sign), $res) === 1);
        }

        if (!$this->checkEmpty($this->paybyPublicKeyPath)) {
            //释放资源
            openssl_free_key($res);
        }

        return $result;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }


}

