<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use BackedEnum;
use League\Uri\UrlPattern;
use League\Uri\UrlPattern\Result;
use League\Uri\UrlPatternBuilder;
use Stringable;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\Url;

final readonly class UrlPatternEmbedResolver implements EmbedResolver, UrlPatternEmbedGenerator
{
    public static function create(
        UrlPatternBuilder $builder,
        UrlPatternEmbedGenerator $embedGenerator,
    ): self {
        return new self($builder->build(), $embedGenerator);
    }

    public function __construct(
        private UrlPattern $pattern,
        private UrlPatternEmbedGenerator $embedGenerator,
    ) {
    }

    public function generateUrl(?Result $arguments): ?Url
    {
        return null === $arguments ? null : $this->embedGenerator->generateUrl($arguments);
    }

    public function generateHtml(?Result $arguments): ?string
    {
        return $this->embedGenerator->generateHtml($arguments);
    }

    public function parse(Url|Stringable|string|Uri|BackedEnum $url): ?Result
    {
        return $this->pattern->extract($url);
    }

    public function url(Url|Uri|Stringable|BackedEnum|string $url): ?Url
    {
        $result = $this->parse($url);

        return null === $result ? null : $this->generateUrl($result);
    }

    public function embed(Url|Stringable|string|Uri|BackedEnum $url): ?string
    {
        return $this->generateHtml($this->parse($url));
    }
}
