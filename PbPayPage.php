<?php

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'lib/PbHttpClient.php';


class PbPayPage {
    private $client;

    public function __construct(){

        $this->client = new PbHttpClient();
    }

    /**
     *
    */
    public function request($builder) {

        $data_content=$builder->getDataContent();

        if(true == $builder->getLogSwitch())
        {
            //打印业务参数
            $this->writeLog($builder->getRequestApi(), $data_content);
        }

        // 首先调用支付api
        $response = $this->client->post($builder->getGatewayUrl(), $builder->getData(), $builder->getParams(), $builder->getHeaderArr());
        // $response = $response->payby_trade_wap_pay_response;

        return $this->progressResponse($builder, $response);
    }

    private function progressResponse($builder, $response){
        $res_header = $response[0];
        $res = json_decode($response[1], true);
        $res_head = $res['head'];

        if("SUCCESS" == @$res_head['applyStatus'] && "0" == @$res_head['code'])
        {
            $res_body = $res['body'];
            $sign = @$res_header['sign'];

            //check verify 返回的sign
            if(!$builder->verify($response[1], $sign) ){
                //如果验签失败，则返回一个自定义的error head
                return array(
                    'head' => array(
                        'applyStatus'=>"SUCCESS",
                        'code'=>"999",
                        'msg'=>"Failed to verify the signature in response header",
                        'traceCode'=>"0",
                    ),
                );
            }

        }

        return $res;
    }

    //请确保项目文件有可写权限，不然打印不了日志。
    private function writeLog($method, $text) {
        // $text=iconv("GBK", "UTF-8//IGNORE", $text);
        //$text = characet ( $text );
        file_put_contents ( dirname ( __FILE__ ).DIRECTORY_SEPARATOR."log.txt", date ( "Y-m-d H:i:s" ) . "  " . $method . "  " . $text . "\r\n", FILE_APPEND );
    }

}