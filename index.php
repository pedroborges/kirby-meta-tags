<?php

@include_once __DIR__.'/vendor/autoload.php';

Kirby::plugin('pedroborges/meta-tags', [
    'pageMethods' => [
        'metaTags' => function ($groups = null) {
            return metaTags($this)->render($groups);
        }
    ]
]);
