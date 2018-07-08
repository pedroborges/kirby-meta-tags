<?php

/**
 * Kirby Meta Tags Plugin
 *
 * @version   1.1.1
 * @author    Pedro Borges <oi@pedroborg.es>
 * @copyright Pedro Borges <oi@pedroborg.es>
 * @link      https://github.com/pedroborges/kirby-meta-tags
 * @license   MIT
 */

// Load dependencies
require __DIR__ . DS . 'vendor' . DS . 'autoload.php';

kirby()->set('page::method', 'metaTags', function ($page, $groups = null) {
    return metaTags($page)->render($groups);
});

function metaTags($page)
{
    return MetaTags::instance($page);
}
