<?php

namespace api\tool\lib;

use Adbar\Dot;
use Psr\Http\Message\ResponseInterface;

class HttpResponse
{
    /**
     * @var array
     */
    private $header = [];

    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $status;

    /**
     * @var Dot
     */
    private $data;

    /**
     * @var mixed
     */
    private $content;

    /**
     * @var ResponseInterface
     */
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
     *
     * @return array|string
     */
    public function getData($key = null)
    {
        $content = null === $this->content ? $this->getContent() : $this->content;
        if (null === $this->data) {
            if (\is_array($content)) {
                $this->data = new Dot($content);
            } else {
                $this->data = $this->body;
            }
        }

        return ($this->data instanceof Dot) ? $this->data->get($key) : $this->data;
    }

    /**
     * @return array|Dot|string
     */
    public function getContent()
    {
        if (null === $this->data) {
            if (\is_object($this->body)) {
                $this->content = Parse::objectToArray($this->body);
            }

            if (\is_string($this->body)) {
                $this->content = Parse::jsonToArray($this->body);
            }

            if (!\is_array($this->content)) {
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
        if (null !== $response) {
            $this->response = $response;
        }

        return $this->response;
    }
}
