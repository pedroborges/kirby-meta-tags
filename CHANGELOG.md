# Changelog
All notable changes to this project will be documented in this file.

## [2.0.2] - 2019-02-22
### Fixed
- Exception thrown when `Kirby\Cms\Page` is passed to `metaTags()`.

## [2.0.1] - 2019-02-21
### Fixed
- Error when loading the `vendor/autoload.php` file in Composer installation.

## [2.0.0] - 2019-02-21
This version adds support to Kirby 3. All options remain and work in the same way as they did in Kirby 2.

For Kirby 2, you can download the [plugin v1.1.1](https://github.com/pedroborges/kirby-meta-tags/archive/v1.1.1.zip) and install it manually in the `site/plugins/meta-tags`.

## [1.1.1] - 2018-07-08
### Fixed
- When capitalizing the `@type` first letter, other capital letters in the word were being converted to lowercase. This has been fixed.

## [1.1.0] - 2018-07-07
### Added
- Support for JSON-LD schema:

    ```php
    'json-ld' => [
        'Organization' => [
            'name' => $site->title()->value(),
            'url' => $site->url(),
            "contactPoint" => [
                '@type' => 'ContactPoint',
                'telephone' => $site->phoneNumber()->value(),
                'contactType' => 'customer service'
            ]
        ]
    ]
    ```

- Support for rendering one tag or a specific group of tags:

    ```php
    <?php echo $page->metaTags('title') ?>

    // or passing an array

    <?php echo $page->metaTags(['og', 'twitter', 'json-ld']) ?>
    ```

### Fixed
- Tags with empty value being rendered with invalid markup.

## [1.0.0] - 2017-11-15
The previous version has been stable enough and no issue has been reported in 8 months :smiley:

I finally got time to make some improvements and update the documentation so it's time to release [v1.0.0](https://github.com/pedroborges/kirby-meta-tags/tree/v1.0.0).

### Changed
- Options `meta-tags.default` and `meta-tags.templates` can now return a `closure` which receives `$page` and `$site` as arguments:

    ```php
    c::set('meta-tags.default', function(Page $page, Site $site) {
        return [
            'title' => $site->title(),
            'meta' => [
                'description' => $site->description()
            ],
            'link' => [
                'canonical' => $page->url()
            ],
            'og' => [
                'title' => $page->isHomePage()
                    ? $site->title()
                    : $page->title(),
                'type' => 'website',
                'site_name' => $site->title(),
                'url' => $page->url()
            ]
        ];
    });
    ```

- Closures in other places now receive `$site` and second argument as well:

    ```php
    'link' => [
        'canonical' => $page->url(),
        'alternate' => function(Page $page, Site $site) {
            $locales = [];

            foreach ($site->languages() as $language) {
                if ($language->isDefault()) continue;

                $locales[] = [
                    'hreflang' => $language->code(),
                    'href' => $page->url($language->code())
                ];
            }

            return $locales;
        }
    ],
    ```

Besides offering a better workflow, these changes also help avoid an issue where `site()` can't be called outside a closure from the config file in multi-language websites, as reported at getkirby/kirby#606.

## [1.0.0-beta] - 2017-03-01
The plugin has gone under heavy refactor and is no longer focused only on Open Graph. For that reason the name has changed from Open Graph to Meta Tags.

Although this is a beta release, it is considered stable and should cover most use cases, even complex ones.

## [1.0.0-alpha] - 2017-02-26
### Initial release
