<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/2 17:52
 */

namespace api\tool\lib;


class HttpResponse
{
    private $header = [];

    private $body;

    private $status;
    
    private $data;

    /**
     * @param $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getData($key = null){
        $data = $this->getContent();

        if(is_null($key)){
            return $data;
        }

        if(false !== strpos($key,'.')){
            $keys = explode('.',$key);
            $tmp = $data;
            foreach ($keys as $k){
                if(isset($tmp[$k])){
                    $tmp = $tmp[$k];
                }else {
                    break;
                }

            }
            return $tmp;
        }

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        if(is_null($this->data)){
            if (is_object($this->body)) {
                $this->data = Parse::objectToArray($this->body);
            }

            if (is_string($this->body)) {
                $this->data = Parse::jsonToArray($this->body);
            }
        }

        return $this->data;
    }
}
