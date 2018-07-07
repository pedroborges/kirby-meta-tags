<?php

namespace PedroBorges\MetaTags;

/**
 * PHP Meta Tags
 *
 * @version   0.0.2
 * @author    Pedro Borges <oi@pedroborg.es>
 * @copyright Pedro Borges <oi@pedroborg.es>
 * @link      https://github.com/pedroborges/meta-tags
 * @license   MIT
 */

class MetaTags
{
    protected $indentation;
    protected $order;

    protected $tags = [];

    /**
     * Create a new instance.
     *
     * @param string  $indentation
     * @param array   $order
     *
     * @return void
     */
    public function __construct($indentation = null, $order = null)
    {
        $this->indentation = $indentation ?: '    ';
        $this->order = $order ?: ['title', 'meta', 'og', 'twitter', 'link', 'json-ld'];
    }

    /**
     * Build an HTML link tag.
     *
     * @param string  $key
     * @param string  $value
     *
     * @return string
     */
    public function link($key, $value)
    {
        if (! empty($value)) {
            $attributes = ['rel' => $key];

            if (is_array($value)) {
                foreach ($value as $key => $v) {
                    $attributes[$key] = $v;
                }
            } else {
                $attributes['href'] = $value;
            }

            $tag = $this->createTag('link', $attributes);

            $this->addToTagsGroup('link', $key, $tag);

            return $tag;
        }
    }

    /**
     * Build an HTML meta tag.
     *
     * @param string  $key
     * @param string  $value
     *
     * @return string
     */
    public function meta($key, $value)
    {
        if (! empty($value)) {
            $attributes = ['name' => $key];

            if (is_array($value)) {
                foreach ($value as $key => $v) {
                    $attributes[$key] = $v;
                }
            } else {
                $attributes['content'] = $value;
            }

            $tag = $this->createTag('meta', $attributes);

            $this->addToTagsGroup('meta', $key, $tag);

            return $tag;
        }
    }

    /**
     * Build an Open Graph meta tag.
     *
     * @param string   $key
     * @param string   $value
     * @param boolean  $prefixed
     *
     * @return string
     */
    public function og($key, $value, $prefixed = true)
    {
        if (! empty($value)) {
            $key = $prefixed ? "og:{$key}" : $key;
            $tag = $this->createTag('meta', [
                'property' => $key,
                'content' => $value
            ]);

            $this->addToTagsGroup('og', $key, $tag);

            return $tag;
        }
    }

    /**
     * Build an JSON linked data meta tag.
     *
     * @param array  $schema
     *
     * @return string
     */
    public function jsonld($schema)
    {
        if (! empty($schema)) {
            $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            $script = "<script type=\"application/ld+json\">\n" . $json . "\n</script>";

            // Fix schema indentation
            $this->tags['json-ld'][] = implode(
                "\n" . $this->indentation,
                explode("\n", $script)
            );

            return $script;
        }
    }

    /**
     * Build a Title HTML tag.
     *
     * @param string  $value
     *
     * @return string
     */
    public function title($value)
    {
        if (! empty($value)) {
            $tag = "<title>{$this->escapeAll($value)}</title>";

            $this->tags['title'][] = $tag;

            return $tag;
        }
    }

    /**
     * Build a Twitter Card meta tag.
     *
     * @param string   $key
     * @param string   $value
     * @param boolean  $prefixed
     *
     * @return string
     */
    public function twitter($key, $value, $prefixed = true)
    {
        if (! empty($value)) {
            $key = $prefixed ? "twitter:{$key}" : $key;
            $tag = $this->createTag('meta', [
                'name' => $key,
                'content' => $value
            ]);

            $this->addToTagsGroup('twitter', $key, $tag);

            return $tag;
        }
    }

    /**
     * Render all or a specific group of HTML meta tags
     *
     * @param mixed  $groups
     *
     * @return string
     */
    public function render($groups = null)
    {
        $groups = ! is_null($groups) ? (array) $groups : $this->order;
        $html = [];

        foreach ($groups as $group) {
            $html[] = $this->renderGroup($group);
        }

        $html = implode('', $html);

        // Remove first indentation
        return preg_replace("/^{$this->indentation}/", '', $html, 1);
    }

    /**
     * Render all HTML meta tags from a specific group.
     *
     * @param string  $group
     *
     * @return string
     */
    protected function renderGroup($group)
    {
        if (! isset($this->tags[$group])) {
            return;
        }

        $html = [];

        foreach ($this->tags[$group] as $tag) {
            if (is_array($tag)) {
                foreach ($tag as $t) {
                    $html[] = $t;
                }
            } else {
                $html[] = $tag;
            }
        }

        return count($html) > 0
                ? $this->indentation . implode("\n" . $this->indentation, $html) . "\n"
                : '';
    }

    /**
     * Add single HTML element to tags group.
     *
     * @param string  $group
     * @param string  $key
     * @param string  $tag
     *
     * @return void
     */
    protected function addToTagsGroup($group, $key, $tag)
    {
        if (isset($this->tags[$group][$key])) {
            $existingTag = $this->tags[$group][$key];

            if (is_array($existingTag)) {
                $this->tags[$group][$key][] = $tag;
            } else {
                $this->tags[$group][$key] = [$existingTag, $tag];
            }
        } else {
            $this->tags[$group][$key] = $tag;
        }
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array  $attributes
     *
     * @return string
     */
    protected function attributes(array $attributes)
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string  $key
     * @param string  $value
     *
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (! is_null($value)) {
            return $key . '="' . $this->escapeAll($value) . '"';
        }
    }

    /**
     * Build an HTML tag
     *
     * @param string  $tagName
     * @param array   $attributes
     *
     * @return string
     */
    protected function createTag($tagName, $attributes)
    {
        if (! empty($tagName) && is_array($attributes)) {
            return "<{$tagName}{$this->attributes($attributes)}>";
        }
    }

    /**
     * Replace special characters with HTML entities
     *
     * @param string  $value
     *
     * @return string
     */
    protected function escapeAll($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8');
    }
}
