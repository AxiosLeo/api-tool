<?php

declare (strict_types = 1);

namespace api\tool\models;

use GuzzleHttp\HandlerStack;
use tpr\Model;

class GuzzleOption extends Model
{
    public string         $base_uri = '';

    public ?HandlerStack $handler = null;

    public AllowRedirects $allow_redirects;

    public ?array $auth = [];

    /**
     * @var mixed|null|\Psr\Http\Message\StreamInterface|resource
     */
    public $body = null;

    public ?array $cert = null;

    public ?\GuzzleHttp\Cookie\CookieJarInterface $cookies = null;

    public ?float $connect_timeout = 15; // second

    public ?bool $debug = false;

    /**
     * @var mixed|false|string 'gzip'
     */
    public $decode_content = true;

    public ?float $delay = 0;

    /**
     * @var bool|integer default 1048576
     */
    public $expect = null;

    public ?array $form_params = null;

    public ?array $headers = null;

    public ?bool $http_errors = true;

    public ?array $json = null;

    public ?array $multipart = null;

    public ?\Closure $on_headers = null;

    public ?\Closure $on_stats = null;

    /**
     * @var string|array
     */
    public $proxy = null;

    /**
     * @var string|array
     */
    public $query = null;

    /**
     * @var mixed|null|\Psr\Http\Message\StreamInterface|resource
     */
    public $sink = null;

    /**
     * @var string|array
     */
    public $ssl_key = null;

    public ?bool $stream = false;

    public ?bool $synchronous = null;

    /**
     * @var bool|string
     */
    public $verify = null;

    public ?float $timeout = 30;

    public ?string $version = null;

    public function __construct(array $data = [])
    {
        $this->allow_redirects = new AllowRedirects();
        parent::__construct($data);
    }
}