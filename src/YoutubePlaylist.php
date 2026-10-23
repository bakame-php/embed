<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use League\Uri\UrlPattern\Result;
use League\Uri\UrlPatternBuilder;
use Uri\WhatWg\Url;

final class YoutubePlaylist extends Youtube
{
    public function __construct()
    {
        parent::__construct(
            static::playlistTemplate(),
            new EmbedList(
                UrlPatternEmbedResolver::create(
                    new UrlPatternBuilder(
                        host: '{(m|music|www).}?youtube.com',
                        path: '/playlist',
                        query: '{*&}?list={:list('.static::REGEXP_YOUTUBE_PLAYLIST_ID.')}{*}',
                    ),
                    $this,
                ),
            )
        );
    }

    public function generateUrl(?Result $arguments): ?Url
    {
        return $this->expand($arguments, ['list' => $arguments?->query->string('list')]);
    }
}
