# Embeds

A proof of concept exploring how 

- [URL Pattern](https://urlpattern.spec.whatwg.org/), implemented by `League\Uri\UrlPattern`, for matching URL components.
- [URI Template](https://uri.thephpleague.com/uri/7.0/uri-template/#variable-extraction), implemented by `League\Uri\UriTemplate`, for template-based variable expansion.

 can be used to simplify URL matching, extraction, and expansion in PHP.

The goal is to demonstrate how these two complementary approaches can be used to build a simple, extensible embedding system.

By describing URL structures and constraints declaratively, they make it easier to support new services or customize existing ones without changing the core API.

## Usage

Embedding a supported URL is as simple as calling `embed()`:

```php
use function Bakame\Embeds\embed;

<p><?= embed('//youtu.be/dQw4w9WgXcQ?t=83') ?></p>
```

This produces the following HTML:

```html
<p><iframe
    width="578"
    height="325"
    src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&start=83"
    title="youtube player"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    referrerpolicy="strict-origin-when-cross-origin"
    allowfullscreen
></iframe></p>
```

## Why URLPattern and UriTemplate?

Both approaches let a resolver describe the URLs it supports without implementing its own URL-matching logic.

With `URLPattern`, constraints can be defined directly on individual URL components. Instead of parsing a URL, extracting its components, and implementing separate validation logic for each one, a pattern describes the URL structure and its constraints in one place.

For example, a YouTube playlist resolver can constrain the playlist identifier in the query component:

```php
use League\Uri\UrlPatternBuilder;

$pattern = new UrlPatternBuilder(
    host: '{(m|music|www).}?youtube.com',
    path: '/playlist',
    query: '{*&}?list={:list([A-Za-z0-9_-]{11})}{*}',
)->build();
```

The pattern describes the accepted URL structure, including the allowed host variations and the format of the playlist identifier. It can then be used to match a URL and extract its variables.

`UriTemplate` offers a complementary, template-based approach. It is particularly useful for describing the destination URL and expanding it with the values extracted from the source URL.

For example, a YouTube playlist embed URL can be represented as:

```php
use League\Uri\UriTemplate;

$uriTemplate = new UriTemplate(
    'https://www.youtube-nocookie.com/embed/videoseries{?list,autoplay,start}'
);
```

Together, these APIs provide a straightforward way to recognize source URLs, extract the relevant values, and generate the corresponding embed URLs.

The project deliberately explores both approaches in a practical application.

## Extensibility

The resolver architecture makes it easy to add support for new providers or adapt existing resolvers to different URL formats.

Resolvers can use `URLPattern` to recognize source URLs and extract their variables, and `UriTemplate` to generate destination URLs. The separation between individual resolvers and the core API keeps provider-specific logic isolated.

Out of the box, the project supports embedding content from Apple Music and YouTube. Other providers can be added by implementing additional resolvers, with room for further features such as display customization.

## Requirements

- PHP 8.4 or later.
- [`league/uri-components`](https://packagist.org/packages/league/uri-components).
- [`league/uri-polyfill`](https://packagist.org/packages/league/uri-polyfill), required on PHP 8.4.

## Status

This project is a proof of concept intended to explore the design and practical use of `URLPattern` and `UriTemplate` in an extensible embedding system. It is not intended to be a fully featured embedding library.