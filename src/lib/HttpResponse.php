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
     * @param mixed $default
     * @return array
     */
    public function getData($key = null, $default = null)
    {
        $data = $this->getContent();
        if ($data instanceof ArrayTool) {
            return $data->get($key, $default);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        if (is_null($this->data)) {
            $data = null;
            if (is_object($this->body)) {
                $data = Parse::objectToArray($this->body);
            } elseif (is_string($this->body)) {
                $data = Parse::jsonToArray($this->body);
            }

            if (is_array($data)) {
                $this->data = ArrayTool::array($data);
            } else {
                $this->data = $this->body;
            }
        }

        return $this->data;
    }
}
