<?php

namespace api\tool;

use api\tool\lib\Parse;
use api\tool\models\GuzzleOption;
use api\tool\models\HttpResponse;
use GuzzleHttp\Client;

/**
 * Class Http.
 *
 */
class Http
{
    private GuzzleOption $options;

    public function __construct($options = [])
    {
        $this->options = new GuzzleOption($options);
    }

    public function configuration(array $config = [])
    {
        if (!empty($config)) {
            $this->options->unmarshall($config);
        }
        return $this->options;
    }

    public function addParam($key, $value)
    {
        if (null === $this->options->form_params) {
            $this->options->form_params = [];
        }
        $this->options->form_params[$key] = $value;
        return $this;
    }

    public function addHeader($key, $value)
    {
        if (null === $this->options->headers) {
            $this->options->headers = [];
        }
        $this->options->headers[$key] = $value;
        return $this;
    }

    public function setBody($body)
    {
        $this->options->body = $body;
        return $this;
    }

    public function setDomain($domain)
    {
        if (0 !== strpos($domain, 'http://') && 0 !== strpos($domain, 'https://')) {
            $domain = 'http://' . $domain;
        }
        $this->options->base_uri = $domain;

        return $this;
    }

    public function send($path = '', $method = 'GET')
    {
        $client = new Client($this->options->toArray());

        $result = $client->request(strtoupper($method), $path);
        unset($client);

        $body     = $result->getBody();
        $response = new HttpResponse();

        $response->guzzle_response = $result;

        $response->headers = $result->getHeaders();
        $content_type      = $result->getHeaderLine('Content-Type');
        $response->content = (string)$body;

        $mimes = new \Mimey\MimeTypes;

        $response->content_type = $mimes->getExtension($content_type);
        if ($response->content_type === 'xml') {
            $response->data = Parse::xmlToArray($response->content);
        } else if ($response->content_type === 'json') {
            $response->data = Parse::jsonToArray($response->content);
        }
        $response->body   = $body;
        $response->status = $result->getStatusCode();
        unset($result, $mimes);
        return $response;
    }
}
