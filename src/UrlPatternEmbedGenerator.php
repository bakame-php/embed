<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use League\Uri\UrlPattern\Result;
use Uri\WhatWg\Url;

interface UrlPatternEmbedGenerator
{
    public function generateUrl(?Result $arguments): ?Url;
    public function generateHtml(?Result $arguments): ?string;
}
