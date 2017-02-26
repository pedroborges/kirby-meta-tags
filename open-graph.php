<?php

/**
 * Kirby Open Graph Plugin
 *
 * @version   1.0.0-alpha
 * @author    Pedro Borges <oi@pedroborg.es>
 * @copyright Pedro Borges <oi@pedroborg.es>
 * @link      https://github.com/pedroborges/kirby-autogit
 * @license   MIT
 */

// Load Composer dependencies
require __DIR__ . DS . 'vendor' . DS . 'autoload.php';

function openGraph($page)
{
    $og = new ChrisKonnertz\OpenGraph\OpenGraph(true);
    $og->template("<meta property=\"{{name}}\" content=\"{{value}}\">\n");

    if (c::get('open-graph.validate', true) && c::get('debug', false)) {
        $og->validate();
    }

    $attributes = null;
    $pageTemplate = $page->intendedTemplate();
    $templates = c::get('open-graph.templates', []);
    $properties = c::get('open-graph.default', [
        'title' => $page->title(),
        'url' => $page->url(),
        'site_name' => site()->title(),
        'type' => 'website'
    ]);

    if (isset($templates[$pageTemplate])) {
        $properties = array_merge($properties, $templates[$pageTemplate]);
    }

    foreach($properties as $tag => $value) {
        if (is_int($tag)) {
            $tag = $value;
            $value = null;
        }

        if ($tag === 'attributes' && is_callable($value)) {
            $attributes = $value($page);
        } elseif (is_callable($value)) {
            $value = $value($page);
        } elseif (is_null($value)) {
            $value = $page->$tag();
        } elseif ($value instanceof Field && $value->isEmpty()) {
            $value = null;
        }

        if (! is_null($attributes) && isset($properties['type'])) {
            $type = $properties['type'];
            $og->attributes($type, $attributes);
        } elseif ($tag === 'locale:alternate' && is_array($value)) {
            foreach ($value as $locale) {
                $og->tag($tag, $locale);
            }
        } elseif (! empty($value)) {
            $og->tag($tag, $value);
        }
    }

    if (count($og->tags()) > 0) {
        return $og->renderTags();
    }
}

kirby()->set('page::method', 'openGraph', function($page) {
    return openGraph($page);
});
