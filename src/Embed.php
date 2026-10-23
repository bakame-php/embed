<?php

declare(strict_types=1);

namespace Bakame\Embeds;

final class Embed
{
    private static ?EmbedList $list = null;

    public static function list(): EmbedList
    {
        return self::$list ??= EmbedList::defaults();
    }

    public static function register(EmbedResolver ...$routes): void
    {
        self::list()->add(...$routes);
    }
}
