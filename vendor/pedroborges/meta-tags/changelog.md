# Change Log
All notable changes to this project will be documented in this file.

## [0.0.2] - 2018-07-07
### Added
- Support for JSON-LD schema:

    ```php
    $tags = new MetaTags;
    $tags->jsonld([
        '@context' => 'http://schema.org',
        '@type': 'Person',
        'name': 'Pedro Borges'
    ]);
    ```

- Support for rendering one tag or a specific group of tags:

    ```php
    <?php echo $tags->render('json-ld') ?>

    // or pass an array

    <?php echo $tags->render(['og', 'twitter']) ?>
    ```

### Fixed
- Tags with empty value being rendered with invalid markup.

## [0.0.1] - 2017-02-28
### Initial release
