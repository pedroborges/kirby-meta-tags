<?php

/**
 * Kirby Meta Tags Plugin
 *
 * @version   1.0.0-beta
 * @author    Pedro Borges <oi@pedroborg.es>
 * @copyright Pedro Borges <oi@pedroborg.es>
 * @link      https://github.com/pedroborges/kirby-meta-tags
 * @license   MIT
 */

// Load dependencies
require __DIR__ . DS . 'vendor' . DS . 'autoload.php';

kirby()->set('page::method', 'metaTags', function($page) {
    return metaTags($page)->render();
});

function metaTags($page)
{
    return MetaTags::instance($page);
}
