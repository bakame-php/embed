<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use League\Uri\UrlPattern\Result;
use League\Uri\UrlPatternBuilder;
use Uri\WhatWg\Url;

final class YoutubeLink extends Youtube
{
    public function __construct()
    {
        parent::__construct(
            static::videoTemplate(),
            new EmbedList(
                UrlPatternEmbedResolver::create(
                    new UrlPatternBuilder(
                        host: 'youtu.be',
                        path: '/:id('.static::REGEXP_YOUTUBE_VIDEO_ID.')',
                    ),
                    $this,
                ),
                UrlPatternEmbedResolver::create(
                    new UrlPatternBuilder(
                        host: '{(m|music|www).}?youtube.com',
                        path: '/:type(embed|live|shorts)/:id('.static::REGEXP_YOUTUBE_VIDEO_ID.')',
                    ),
                    $this,
                ),
            )
        );
    }

    public function generateUrl(?Result $arguments): ?Url
    {
        return $this->expand($arguments, ['id' => $arguments?->path->string('id')]);
    }
}
