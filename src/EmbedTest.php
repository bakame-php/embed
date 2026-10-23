<?php

declare(strict_types=1);

namespace Bakame\Embeds;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EmbedTest extends TestCase
{
    #[DataProvider('provideUrls')]
    public function test_embed_url(string $url, ?string $expected): void
    {
        self::assertSame($expected, embed_url($url)?->toAsciiString());
    }

    /** @return iterable<string, array{string, ?string}> */
    public static function provideUrls(): iterable
    {
        yield 'YouTube watch URL' => [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1',
        ];

        yield 'Youtube short URL' => [
            '//youtu.be/dQw4w9WgXcQ?t=83',
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&start=83',
        ];

        yield 'Youtube playlist URL' => [
            '//m.youtube.com/playlist?list=PL9zDdgiGjkIdQwiNb-1evZqGmDfmOwIHv',
            'https://www.youtube-nocookie.com/embed/videoseries?list=PL9zDdgiGjkIdQwiNb-1evZqGmDfmOwIHv&autoplay=1',
        ];

        yield 'Apple Music song' => [
            'https://music.apple.com/us/song/come-together/1441164426?i=1441164430',
            'https://embed.music.apple.com/us/song/come-together/1441164426?i=1441164430',
        ];

        yield 'Apple Music album' => [
            '//music.apple.com/us/album/abbey-road-remastered/1441164426',
            'https://embed.music.apple.com/us/album/abbey-road-remastered/1441164426',
        ];

        yield 'Apple Music playlist' => [
            'https://music.apple.com/us/playlist/todays-hits/pl.f4d106fed2bd41149aaacabb233eb5eb',
            'https://embed.music.apple.com/us/playlist/todays-hits/pl.f4d106fed2bd41149aaacabb233eb5eb',
        ];

        yield 'unsupported URL' => [
            'https://example.com/video',
            null,
        ];

        yield 'unsupported Youtube URL' => [
            '//m.youtube.com/foobar/dQw4w9WgXcQ?t=83',
            null,
        ];
    }
}
