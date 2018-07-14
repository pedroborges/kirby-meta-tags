<?php

use Kirby\Cms\Page;
use PedroBorges\KirbyMetaTags\MetaTags;

Kirby::plugin('pedroborges/metatags', [
    'pageMethods' => [
        'metaTags' => function ($groups = null) {
            return metaTags($this)->render($groups);
        }
    ]
]);

if (! function_exists('metaTags')) {
    /**
     * Generate meta tags for a given page.
     *
     * @param  Page  $page
     * @return MetaTags
     */
    function metaTags(Page $page) : MetaTags
    {
        return MetaTags::instance($page);
    }
}
