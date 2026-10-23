<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use BackedEnum;
use Countable;
use IteratorAggregate;
use Stringable;
use Traversable;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\Url;

/**
 * @implements IteratorAggregate<EmbedResolver>
 */
final class EmbedList implements Countable, EmbedResolver, IteratorAggregate
{
    /** @var list<EmbedResolver> */
    private array $resolvers = [];

    public function __construct(EmbedResolver ...$resolvers)
    {
        $this->add(...$resolvers);
    }

    public function count(): int
    {
        return count($this->resolvers);
    }

    /**
     * @return Traversable<EmbedResolver>
     */
    public function getIterator(): Traversable
    {
        yield from $this->resolvers;
    }

    public function add(EmbedResolver ...$resolvers): self
    {
        foreach ($resolvers as $resolver) {
            $this->resolvers[] = $resolver;
        }

        return $this;
    }

    public function url(Url|Uri|Stringable|BackedEnum|string $url): ?Url
    {
        foreach ($this as $resolver) {
            $embedUrl = $resolver->url($url);
            if (null === $embedUrl) {
                continue;
            }

            return $embedUrl;
        }

        return null;
    }

    public function embed(Url|Uri|Stringable|BackedEnum|string $url): ?string
    {
        foreach ($this as $resolver) {
            $result = $resolver->embed($url);
            if (null === $result) {
                continue;
            }

            return $result;
        }

        return null;
    }

    public static function defaults(): self
    {
        return new self(
            new AppleMusic(),
            new YoutubeLink(),
            new YoutubePlaylist(),
            new YoutubeWatch(),
        );
    }
}
