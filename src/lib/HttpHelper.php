<?php

namespace api\tool\lib;

use Adbar\Dot;
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
        'xml_root_node_name' => 'data',
    ];

    private $method = 'POST';

    private $domain = '';

    private $param;

    public function __construct($options)
    {
        $options       = array_merge($this->options, $options);
        $this->options = new Dot($options);
        $this->param   = new Dot([]);
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

        if (\is_array($header_name)) {
            $this->options['headers'] = array_merge($this->options['headers'], $header_name);
        } else {
            $this->options->set('headers.' . $header_name, $header_content);
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
     * @param mixed $key
     * @param mixed $value
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
     */
    public function curl($path = '', $data = [])
    {
        $this->setParam($data);
        $data = $this->param->get();

        if (empty($this->domain) && false !== strpos($path, 'http')) {
            $url = parse_url($path);
            if (4 === $url['scheme']) {
                $this->domain = 'http://' . $url['host'];
            } else {
                $this->domain = 'https://' . $url['host'];
            }
        }

        $client = new Client(['base_uri' => $this->domain]);

        if (HttpMethod::POST === $this->method) {
            if ('array' === $this->options['format']) {
                $this->options->set('form_params', $data);
            }
            if (empty($this->options->get('body'))) {
                if ('json' === $this->options['format']) {
                    $this->setHeader('Content-Type', 'application/json; charset=UTF8');
                    $this->options->set('body', Parse::arrayToJson($data));
                } elseif ('xml' === $this->options['format']) {
                    $this->setHeader('Content-Type', 'text/xml; charset=UTF8');
                    $this->options->set('body', Parse::ArrayToXml($data, $this->options->get('xml_root_node_name')));
                }
            }
        } elseif (!empty($data)) {
            $path = $path . '?' . $this->formatParam($data);
        }

        $result   = $client->request($this->method, $path, $this->options->get());
        $body     = $result->getBody();
        $response = new HttpResponse();
        $response->guzzleResponse($result);
        $response->setHeader($result->getHeaders());
        $content_type = $result->getHeaderLine('Content-Type');

        $body = (string) $body;
        if (false !== strpos($content_type, 'xml')) {
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
        $str = '';
        $n   = 0;
        $arr = [];
        foreach ($param as $k => $v) {
            if ($this->options['urlencode']) {
                $v = rawurlencode($v);
            }
            $arr[$n++] = $k . '=' . $v;
        }
        implode('&', $arr);

        return $str;
    }
}
