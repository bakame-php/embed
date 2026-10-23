<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use BackedEnum;
use League\Uri\Components\URLSearchParams;
use League\Uri\UriTemplate;
use League\Uri\UrlPattern\Result;
use Stringable;
use Throwable;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\InvalidUrlException;
use Uri\WhatWg\Url;

use function htmlspecialchars;
use function preg_match;
use function str_replace;

use const ENT_QUOTES;
use const ENT_SUBSTITUTE;

abstract class Youtube implements EmbedResolver, UrlPatternEmbedGenerator
{
    protected const string REGEXP_YOUTUBE_PLAYLIST_ID = '[A-Za-z0-9_-]+';
    protected const string REGEXP_YOUTUBE_VIDEO_ID = '[A-Za-z0-9_-]{11}';
    protected const string REGEXP_DURATION_FORMAT = '/^
        (?:(?<hours>\d+)h)?
        (?:(?<minutes>\d+)m)?
        (?:(?<seconds>\d+)s?)?
    $/x';
    protected const float RATIO_PLAYER = 16 / 9;
    protected const float RATIO_SHORTS = 9 / 16;
    protected const int EMBED_WITDH = 578;
    protected const string EMBED_TEMPLATE = <<<EOF
<iframe 
    width="{width}"
    height="{height}" 
    src="{url}" 
    title="youtube player"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    referrerpolicy="strict-origin-when-cross-origin" 
    allowfullscreen
  >
</iframe>
EOF;

    protected function __construct(
        protected UriTemplate $template,
        protected EmbedResolver $resolver
    ) {
    }

    final public function url(Url|Uri|Stringable|BackedEnum|string $url): ?Url
    {
        return $this->resolver->url($url);
    }

    final public function embed(Url|Uri|Stringable|BackedEnum|string $url): ?string
    {
        return $this->resolver->embed($url);
    }

    abstract public function generateUrl(?Result $arguments): ?Url;

    final public function generateHtml(?Result $arguments): ?string
    {
        if (null === $arguments) {
            return null;
        }

        $ratio = 'shorts' === $arguments->path->string('type') ? static::RATIO_SHORTS : static::RATIO_PLAYER;
        $width = static::EMBED_WITDH;
        $height = (int) ($width / $ratio);

        return str_replace(
            ['{height}', '{width}', '{url}'],
            array_map(
                static fn (string|float|int|null|Stringable $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                [(string) $height, (string) $width, $this->generateUrl($arguments)?->toAsciiString() ?? '']
            ),
            static::EMBED_TEMPLATE,
        );
    }

    /**
     * @param array<non-empty-string, string|int|null|list<string|int|null>> $data
     *
     * @throws InvalidUrlException
     */
    protected function expand(?Result $arguments, array $data): ?Url
    {
        if (null === $arguments) {
            return null;
        }

        unset($data['autoplay'], $data['start']);
        $data['autoplay'] = 1;
        $start = static::mediaStart($arguments->query->input);
        if (0 < $start) {
            $data['start'] = $start;
        }

        return $this->template->expandToUrl($data);
    }

    protected static function mediaStart(string $queryString): int
    {
        try {
            $query = URLSearchParams::new($queryString);
        } catch (Throwable) {
            return 0;
        }

        $duration = $query->string('t', $query->string('start'));
        if (null !== $duration && 1 === preg_match(static::REGEXP_DURATION_FORMAT, $duration, $matches)) {
            return (int) ($matches['hours'] ?? 0) * 3_600
                + (int) ($matches['minutes'] ?? 0) * 60
                + (int) ($matches['seconds'] ?? 0);
        }

        return 0;
    }

    protected static function playlistTemplate(): UriTemplate
    {
        static $template;

        if (!$template instanceof UriTemplate) {
            $template = new UriTemplate('https://www.youtube-nocookie.com/embed/videoseries{?list,autoplay,start}');
        }

        return $template;
    }

    protected static function videoTemplate(): UriTemplate
    {
        static $template;

        if (!$template instanceof UriTemplate) {
            $template = new UriTemplate('https://www.youtube-nocookie.com/embed/{id}{?list,autoplay,start}');
        }

        return $template;
    }
}
