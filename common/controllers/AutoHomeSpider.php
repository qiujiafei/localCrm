<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\controllers;

class AutoHomeSpider {

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var resource
     */
    protected $curl;

    /**
     * @var string
     */
    protected $result;

    /**
     * @var array
     */
    protected $defaultHeader = [
//        "content-type: application/x-www-form-urlencoded; charset=gb2312",
        "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
        "accept-encoding: gzip, deflate, br",
        "accept-language: zh-CN,zh;q=0.9",
        "dnt: 1",
        "upgrade-insecure-requests: 1",
        "user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36"
    ];

    /**
     * Contructor
     *
     * @var string $url
     * @var array $header
     */
    public function __construct(string $url = null, array $header = null) {
        if (!extension_loaded('curl')) {
            throw new Exception\RuntimeException(sprintf(
                    '%s is need in ApiVisitor.', 'Curl extentsion'
            ));
        }

        $this->url = $url;
        if (empty($header)) {
            $header = $this->defaultHeader;
        }
        $this->header = $header;

        $this->init();
    }

    /**
     * Invoke
     *
     * @return string
     */
    public function __invoke() {
        return $this->getResponse();
    }

    /**
     * Init CURL and it's options
     *
     * @return object
     */
    public function init($url = null, $header = null) {
        $this->curl = curl_init();

//        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($url) {
            curl_setopt($this->curl, CURLOPT_URL, $url);
        } else {
            curl_setopt($this->curl, CURLOPT_URL, $this->url);
        }

        curl_setopt($this->curl, CURLOPT_ENCODING, 'UTF-8');

        if ($header) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
        } elseif (!empty($this->header)) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->defaultHeader);
        }
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($this->curl, CURLOPT_SSLVERSION, 1); //设置SSL协议版本号

        return $this;
    }

    /**
     * Get response string from target URL
     *
     * @return string
     */
    public function getResponse() {
        unset($this->result);
        ob_start();
        if (curl_exec($this->curl)) {
            $this->result = ob_get_contents();
        }
        ob_end_clean();
        if (curl_errno($this->curl)) {
            print curl_error($this->curl);
        }
        $this->result = $this->result ? $this->result : false;

        if ($this->result) {
            $this->result = mb_convert_encoding($this->result, 'UTF-8', 'GB2312');
            $this->result = preg_replace("/\s+/", " ", $this->result); //过滤多余回车
            $this->result = preg_replace("/<[ ]+/si", "<", $this->result);
        }

        return $this->result;
    }

}

