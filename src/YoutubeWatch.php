<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use Exception;
use League\Uri\Components\URLSearchParams;
use League\Uri\UrlPattern\Result;
use League\Uri\UrlPatternBuilder;
use Uri\WhatWg\Url;

final class YoutubeWatch extends Youtube
{
    public function __construct()
    {
        parent::__construct(
            self::videoTemplate(),
            new EmbedList(
                UrlPatternEmbedResolver::create(
                    new UrlPatternBuilder(
                        host: '{(m|music|www).}?youtube.com',
                        path: '/watch',
                        query: '{*&}?v={:id('.static::REGEXP_YOUTUBE_VIDEO_ID.')}{*}',
                    ),
                    $this,
                ),
            )
        );
    }

    public function generateUrl(?Result $arguments): ?Url
    {
        if (null === $arguments) {
            return null;
        }

        $data = ['id' => $arguments->query->string('id')];
        try {
            $query = URLSearchParams::new($arguments->query->input);
            if ($query->has('list')) {
                $data['list'] = $query->string('list');
            }
        } catch (Exception) {

        }

        return $this->expand($arguments, $data);
    }
}
