<?php

use PedroBorges\MetaTags\MetaTags as Head;

class MetaTags
{
    public $tags;

    protected static $instance = null;

    protected $indentation;
    protected $order;
    protected $page;

    public function __construct(Page $page)
    {
        $this->indentation = c::get('meta-tags.indentation', null);
        $this->order = c::get('meta-tags.order', null);
        $this->tags = new Head($this->indentation, $this->order);

        $templates = c::get('meta-tags.templates', []);
        $default = c::get('meta-tags.default', [
            'title' => page()->isHomePage() ? site()->title() : page()->title(),
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
        $this->data = $default;

        if (isset($templates[$page->intendedTemplate()])) {
            $this->data = a::merge($this->data, $templates[$page->intendedTemplate()]);
        }

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

    public function render()
    {
        $this->addTagsFromTemplate();

        return $this->tags->render();
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
            $value = $value($this->page);
        } elseif ($value instanceof Field && $value->isEmpty()) {
            $value = null;
        }

        if ($group === 'title') $tag = $value;

        if (is_array($value)) {
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

    public function __call($method, $arguments) {
        if( method_exists($this->tags, $method)) {
            return call_user_func_array(array($this->tags, $method), $arguments);
        } else {
            throw new Exception('Invalid method: ' . $method);
        }
    }

}
