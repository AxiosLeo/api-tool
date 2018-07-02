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
    private $header;

    private $body;

    private $status;

    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getContent()
    {
        if(is_object($this->body)){
            $this->body = Parse::objectToArray($this->body);
        }

        if(is_string($this->body)){
            $this->body = Parse::jsonToArray($this->body);
        }

        return $this->body;
    }
}
