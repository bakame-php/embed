<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use BackedEnum;
use League\Uri\UriTemplate;
use League\Uri\UrlPattern;
use League\Uri\UrlPattern\Result;
use League\Uri\UrlPatternBuilder;
use Stringable;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\InvalidUrlException;
use Uri\WhatWg\Url;

use function array_map;
use function htmlspecialchars;

use const ENT_QUOTES;
use const ENT_SUBSTITUTE;

final class AppleMusic implements EmbedResolver
{
    private const int EMBED_PLAYLIST_HEIGHT = 450;
    private const int EMBED_SINGLE_HEIGHT = 175;
    private const string EMBED_TEMPLATE = <<<EOF
<iframe 
    allow="autoplay *; encrypted-media *; fullscreen *; clipboard-write" 
    frameborder="0" 
    height="{height}" 
    style="width:100%;max-width:660px;overflow:hidden;border-radius:10px;"
    sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation"
    src="{url}"
></iframe>
EOF;

    /**
     * @throws InvalidUrlException
     */
    public function url(Url|Uri|Stringable|BackedEnum|string $url): ?Url
    {
        return self::generateUrl(self::pattern()->extract($url));
    }

    /**
     * @throws InvalidUrlException
     */
    public function embed(Url|Uri|Stringable|BackedEnum|string $url): ?string
    {
        $arguments = self::pattern()->extract($url);
        if (null === $arguments) {
            return null;
        }

        $height = AppleMusicType::Song === $arguments->path->enum('type', AppleMusicType::class)
            || null !== $arguments->query->integer('i')
                ? self::EMBED_SINGLE_HEIGHT
                : self::EMBED_PLAYLIST_HEIGHT;

        return str_replace(
            ['{height}', '{url}'],
            array_map(
                static fn (string|float|int|null|Stringable $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                [(string) $height, self::generateUrl($arguments)?->toAsciiString() ?? '']
            ),
            self::EMBED_TEMPLATE,
        );
    }

    /**
     * @throws InvalidUrlException
     */
    private static function generateUrl(?Result $arguments): ?Url
    {
        if (null === $arguments) {
            return null;
        }

        $variables = [...$arguments->path->variables(), ...$arguments->query->variables()];
        if (null === $arguments->query->integer('i')) {
            unset($variables['i']);
        }

        return self::template()->expandToUrl($variables);
    }

    private static function pattern(): UrlPattern
    {
        static $pattern;

        if (!$pattern instanceof UrlPattern) {
            $pattern = new UrlPatternBuilder(
                host: 'music.apple.com',
                path: '/:tld([a-z]{2})/:type(album|playlist|song)/:name{/:id}?',
                query: '{*&}?{i=:i(\d+)}?{*}'
            )->build();
        }

        return $pattern;
    }

    private static function template(): UriTemplate
    {
        static $template;

        if (!$template instanceof UriTemplate) {
            $template = new UriTemplate('https://embed.music.apple.com/{tld}/{type}/{name}{/id}{?i}');
        }

        return $template;
    }
}
