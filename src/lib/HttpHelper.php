<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/2 17:18
 */

namespace api\tool\lib;

use api\tool\Http;
use GuzzleHttp\Client;

class HttpHelper
{
    private $options = [
        'http_errors'        => false,
        'connect_timeout'    => 30,
        'read_timeout'       => 80,
        'urlencode'          => 1,
        'format'             => 'array',  //array|json|xml
        'separator'          => '.',
        'xml_root_node_name' => 'data'
    ];

    private $method = "POST";

    private $domain = "";

    private $param;

    private $separator;

    public function __construct($options)
    {
        $options         = array_merge($this->options, $options);
        $this->separator = $options['separator'];
        $this->options   = ArrayTool::instance($options, $this->separator);
        $this->separator = $this->options['separator'];
        $this->param     = ArrayTool::instance([], $this->separator);
    }

    /**
     * @param        $header_name
     * @param string $header_content
     *
     * @return $this
     */
    public function setHeader($header_name, $header_content = '')
    {
        if (!isset($this->options['headers'])) {
            $this->setOption('headers', []);
        }

        if (is_array($header_name)) {
            $this->options['headers'] = array_merge($this->options['headers'], $header_name);
        } else {
            $this->options['headers' . $this->separator . $header_name] = $header_content;
        }

        return $this;
    }

    /**
     * @param       $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setParam($key, $value = null)
    {
        $this->param->set($key, $value);
        return $this;
    }

    public function getParam()
    {
        return $this->param->get();
    }

    /**
     * @param string|int $key
     * @param mixed      $value
     *
     * @return $this
     */
    public function setOption($key, $value = '')
    {
        $this->options->set($key, $value);
        return $this;
    }

    public function getOption()
    {
        return $this->options->get();
    }

    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * @param $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        if (false === strpos($domain, 'http')) {
            $domain = 'http://' . $domain;
        }
        $this->domain = $domain;
        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->setOption('format', $format);
        return $this;
    }

    /**
     * @param $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->setOption('body', $body);
        return $this;
    }

    /**
     * @param string $path
     * @param array  $data
     *
     * @return HttpResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function curl($path = '', $data = [])
    {
        $this->setParam($data);
        $data = $this->param->get();

        if (empty($this->domain) && false !== strpos($path, 'http')) {
            $url = parse_url($path);
            if ($url['scheme'] === 4) {
                $this->domain = "http://" . $url['host'];
            } else {
                $this->domain = "https://" . $url['host'];
            }
        }

        $client = new Client(['base_uri' => $this->domain]);

        if ($this->method === HttpMethod::POST) {
            if ($this->options['format'] === 'array') {
                $this->options->set('form_params', $data);
            }
            if (empty($this->options->get('body'))) {
                if ($this->options['format'] === 'json') {
                    $this->setHeader("Content-Type", "application/json; charset=UTF8");
                    $this->options->set('body', Parse::arrayToJson($data));
                } else if ($this->options['format'] === 'xml') {
                    $this->setHeader("Content-Type", "text/xml; charset=UTF8");
                    $this->options->set('body', Parse::ArrayToXml($data, $this->options->get('xml_root_node_name')));
                }
            }
        } else if (!empty($data)) {
            $path = $path . '?' . $this->formatParam($data);
        }

        $result   = $client->request($this->method, $path, $this->options->get());
        $body     = $result->getBody();
        $response = new HttpResponse();
        $response->setHeader($result->getHeaders());
        $content_type = $result->getHeaderLine("Content-Type");

        $body = (string)$body;
        if (strpos($content_type, "xml") !== false) {
            $body = Parse::xmlToArray($body);
        }

        $response->setBody($body)
            ->setStatus($result->getStatusCode());

        Http::clear();

        return $response;
    }

    public function formatParam($param)
    {
        ksort($param);
        $str = "";
        $n   = 0;
        foreach ($param as $k => $v) {
            if ($this->options['urlencode']) {
                $v = rawurlencode($v);
            }
            if ($n) {
                $str .= "&";
            }
            $str .= $k . '=' . $v;
            $n++;
        }
        return $str;
    }
}