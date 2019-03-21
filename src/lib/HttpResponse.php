<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/2 17:52
 */

namespace api\tool\lib;

use Psr\Http\Message\ResponseInterface;

class HttpResponse
{
    private $header = [];

    private $body;

    private $status;

    private $data;

    private $content;

    private $response;

    /**
     * @param $header
     *
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
     *
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
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * 直接获取body内容，不进行格式处理.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $key
     * @param string $separator 分隔符 用于方便查询子节点
     *
     * @return array
     */
    public function getData($key = null, $separator = '.')
    {
        $content = is_null($this->content) ? $this->getContent() : $this->content;
        if (is_null($this->data)) {
            if (is_array($content)) {
                $this->data = ArrayTool::instance($content, $separator);
            } else {
                $this->data = $this->body;
            }
        }

        return is_object($this->data) ? $this->data->get($key) : $this->data;
    }

    /**
     * @return array|ArrayTool|string
     */
    public function getContent()
    {
        if (is_null($this->data)) {
            if (is_object($this->body)) {
                $this->content = Parse::objectToArray($this->body);
            }

            if (is_string($this->body)) {
                $this->content = Parse::jsonToArray($this->body);
            }

            if (!is_array($this->content)) {
                $this->content = $this->body;
            }
        }

        return $this->content;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function guzzleResponse($response = null)
    {
        if (!is_null($response)) {
            $this->response = $response;
        }
        return $this->response;
    }
}
