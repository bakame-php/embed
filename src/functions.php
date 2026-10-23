<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use BackedEnum;
use Stringable;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\Url;

function embed_url(Url|Uri|Stringable|BackedEnum|string $url): ?Url
{
    return Embed::list()->url($url);
}

function embed(Url|Uri|Stringable|BackedEnum|string $url): ?string
{
    return Embed::list()->embed($url);
}
