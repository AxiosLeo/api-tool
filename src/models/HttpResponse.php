<?php

declare (strict_types = 1);

namespace api\tool\models;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use tpr\Model;

class HttpResponse extends Model
{
    public ?ResponseInterface $guzzle_response = null;

    /**
     * @var string|StreamInterface|resource
     */
    public $body;

    public ?array $data = null;

    public ?string $content = null;

    public array $headers = [];

    public ?int $status = null;

    public ?string $content_type = '';
}