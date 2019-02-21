<?php

use Kirby\Cms\Page;
use PedroBorges\KirbyMetaTags\MetaTags;

if (! function_exists('metaTags')) {
    /**
     * Generate meta tags for a given page.
     *
     * @param  Kirby\Cms\Page  $page
     * @return MetaTags
     */
    function metaTags(Page $page): MetaTags
    {
        return MetaTags::instance($page);
    }
}
