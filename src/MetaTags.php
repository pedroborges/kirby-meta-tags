<?php

namespace PedroBorges\KirbyMetaTags;

use Exception;
use Kirby\Toolkit\A;
use Kirby\Cms\Field;
use Kirby\Cms\Page;
use PedroBorges\MetaTags\MetaTags as Tags;

class MetaTags
{
    public $tags;

    protected static $instance = null;

    protected $indentation;
    protected $order;
    protected $page;

    public function __construct(Page $page)
    {
        $this->indentation = option('pedroborges.meta-tags.indentation', null);
        $this->order = option('pedroborges.meta-tags.order', null);
        $this->tags = new Tags($this->indentation, $this->order);

        $templates = option('pedroborges.meta-tags.templates', []);
        $default = option('pedroborges.meta-tags.default', [
            'title' => $page->isHomePage() ? site()->title() : $page->title(),
            'meta' => [
                'description' => site()->description()
            ],
            'link' => [
                'canonical' => $page->url()
            ],
            'og' => [
                'title' => $page->title(),
                'type' => 'website',
                'site_name' => site()->title(),
                'url' => $page->url()
            ]
        ]);

        $this->page = $page;
        $this->data = is_callable($default) ? $default($page, site()) : $default;
        $templates = is_callable($templates) ? $templates($page, site()) : $templates;

        if (! is_array($this->data)) {
            throw new Exception('Option "pedroborges.meta-tags.default" must return an array');
        }

        if (! is_array($templates)) {
            throw new Exception('Option "pedroborges.meta-tags.templates" must return an array');
        }

        if (isset($templates[$page->template()->name()])) {
            $this->data = A::merge($this->data, $templates[$page->template()->name()]);
        }

        $this->addTagsFromTemplate();

        static::$instance = $this;
    }

    /**
    * Return an existing instance or create a new one.
    *
    * @param  Page  $page
    *
    * @return HeadTags
    */
    public static function instance($page)
    {
        return static::$instance = is_null(static::$instance)
            ? new static($page)
            : static::$instance;
    }

    public function render($groups = null)
    {
        return $this->tags->render($groups);
    }

    protected function addTagsFromTemplate()
    {
        foreach ($this->data as $group => $tags) {
            if ($group === 'title') {
                $this->addTag('title', $this->data[$group], $group);
                continue;
            }

            $this->addTagsFromGroup($group, $tags);
        }
    }

    protected function addTagsFromGroup($group, $tags)
    {
        foreach ($tags as $tag => $value) {
            $this->addTag($tag, $value, $group);
        }
    }

    protected function addTag($tag, $value, $group)
    {
        if (is_callable($value)) {
            $value = $value($this->page, site());
        } elseif ($value instanceof Field && $value->isEmpty()) {
            $value = null;
        }

        if ($group === 'title') {
            $tag = $value;
        }

        if ($group === 'json-ld') {
            $this->addJsonld($tag, $value);
        } elseif (is_array($value)) {
            $this->addTagsArray($tag, $value, $group);
        } elseif (! empty($value)) {
            $this->tags->$group($tag, $value);
        }
    }

    protected function addTagsArray($tag, $value, $group)
    {
        foreach ($value as $key => $v) {
            if (strpos($tag, 'namespace:') === 0) {
                $prefix = str_replace('namespace:', '', $tag);
                $name = $prefix !== $key ? "{$prefix}:{$key}" : $key;

                $this->addTag($name, $v, $group);
            } else {
                if (is_numeric($key)) {
                    $this->addTag($tag, $v, $group);
                } else {
                    $this->tags->$group($tag, $value);
                    break;
                }
            }
        }
    }

    protected function addJsonld($type, $schema)
    {
        $schema = array_reverse($schema, true);

        if (! isset($schema['@type'])) {
            $schema['@type'] = ucfirst($type);
        }

        if (! isset($schema['@context'])) {
            $schema['@context'] = 'http://schema.org';
        }

        $this->tags->jsonld(array_reverse($schema, true));
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->tags, $method)) {
            return call_user_func_array([$this->tags, $method], $arguments);
        } else {
            throw new Exception('Invalid method: ' . $method);
        }
    }
}
