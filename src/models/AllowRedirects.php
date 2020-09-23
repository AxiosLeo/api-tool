<?php

declare(strict_types=1);

namespace api\tool\models;

use tpr\Model;

class AllowRedirects extends Model
{
    public int       $max             = 10;   // allow at most 10 redirects.
    public bool      $strict          = true; // use "strict" RFC compliant redirects.
    public bool      $referer         = true; // add a Referer header
    public array     $protocols       = ['https']; // only allow https URLs
    public ?\Closure $on_redirect     = null;
    public bool      $track_redirects = true;
}
