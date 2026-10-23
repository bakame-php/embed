<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use BackedEnum;
use Stringable;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\Url;

interface EmbedResolver
{
    public function url(Url|Uri|Stringable|BackedEnum|string $url): ?Url;
    public function embed(Url|Uri|Stringable|BackedEnum|string $url): ?string;
}
