<?php
/**
 * Http Client
 */
class PbHttpClient{
    /**
     * HttpClient
     * @param array $headers HTTP header
     */
    public function __construct(){
        $this->connectTimeout = 60000;
        $this->socketTimeout = 60000;
    }

    /**
     * 连接超时
     * @param int $ms 毫秒
     */
    public function setConnectionTimeoutInMillis($ms){
        $this->connectTimeout = $ms;
    }

    /**
     * 响应超时
     * @param int $ms 毫秒
     */
    public function setSocketTimeoutInMillis($ms){
        $this->socketTimeout = $ms;
    }


    /**
     * @param  string $url
     * @param  array $data HTTP POST BODY
     * @param  array $param HTTP URL
     * @param  array $headers HTTP header
     * @return array
     */
    public function post($url, $data=array(), $params=array(), $headers=array()){
        $url = $this->buildUrl($url, $params);
        $headers = $this->buildHeaders($headers);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));

        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->socketTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ch);

        #print_r($url);
        #print_r($headers);
        #$cgif = curl_getinfo($ch);

        #print_r($cgif['url']);
        #print_r($cgif['request_header']);

        if (curl_errno($ch)) {

            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($response, $httpStatusCode);
            }
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $response = $this->getContent($response, $headerSize);

        return $response;
    }

    /**
     * @param  string $url
     * @param  array $param HTTP URL
     * @param  array $headers HTTP header
     * @return array
     */
    public function get($url, $params=array(), $headers=array()){
        $url = $this->buildUrl($url, $params);
        $headers = $this->buildHeaders($headers);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->socketTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($response, $httpStatusCode);
            }
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $response = $this->getContent($response, $headerSize);

        return $response;
    }


    /**
     * http请求头信息分析
     * @param unknown $response
     * @param unknown $headerSize
     * @return NULL[]
     */
    private function getContent($response, $headerSize) {
        $rtn = [[], ""];
        $split = "\r\n\r\n";
        // 分离头信息
        $headerStr = trim(substr($response, 0, $headerSize), $split);
        // 分离正文
        $body = trim(substr($response, $headerSize));

        $headerArr = explode("\n", $headerStr);
        if (!empty($headerArr)) {
            $header = [];
            foreach ($headerArr as $headRow) {
                $v = explode(':', $headRow);
                if ($v) {
                    $header[trim($v[0])] = isset($v[1]) ? trim($v[1]) : "";
                }
            }
            if (!empty($header))
                $rtn[0] = $header;
        }
        if (!empty($body))
            $rtn[1] = $body;

        return $rtn;
    }

    /**
     * 构造 header
     * @param  array $headers
     * @return array
     */
    private function buildHeaders($headers){
        $result = array();
        foreach($headers as $k => $v){
            $result[] = sprintf('%s:%s', $k, $v);
        }
        return $result;
    }

    /**
     *
     * @param  string $url
     * @param  array $params 参数
     * @return string
     */
    private function buildUrl($url, $params){
        if(!empty($params)){
            $str = http_build_query($params);
            return $url . (strpos($url, '?') === false ? '?' : '&') . $str;
        }else{
            return $url;
        }
    }
}
