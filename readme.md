# Kirby Open Graph (WIP) [![Release](https://img.shields.io/github/release/pedroborges/kirby-open-graph.svg)](https://github.com/pedroborges/kirby-open-graph/releases) [![Issues](https://img.shields.io/github/issues/pedroborges/kirby-open-graph.svg)](https://github.com/pedroborges/kirby-open-graph/issues)

A Kirby CMS plugin that builds [Open Graph](http://ogp.me) meta tags for your site.

## Requirements
- Kirby 2.3.2+
- PHP 5.4+

## Installation

### Download
[Download the files](https://github.com/pedroborges/kirby-open-graph/archive/master.zip) and place them inside `site/plugins/open-graph`.

### Kirby CLI
Kirby's [command line interface](https://github.com/getkirby/cli) is the easiest way to install the Open Graph plugin:

    $ kirby plugin:install pedroborges/kirby-open-graph

To update it simply run:

    $ kirby plugin:update pedroborges/kirby-open-graph

### Git Submodule
You can add the Open Graph as a Git submodule.

<details>
    <summary><strong>Show Git Submodule instructions</strong> 👁</summary><p>

    $ cd your/project/root
    $ git submodule add https://github.com/pedroborges/kirby-open-graph.git site/plugins/open-graph
    $ git submodule update --init --recursive
    $ git commit -am "Add Open Graph Git"

Updating is as easy as running a few commands.

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive

</p></details>

## Usage
Once installed, you can use Open Graph in two ways:

### Page method
    <?php echo $page->openGraph() ?>

### Function
    <?php echo openGraph($page) ?>

Add your preferred method in the `head` of your HTML page and you are done! Well... not so fast. You will probably want to tweak the default meta tags or add new ones, so head over to the next section.

## Options
The Open Graph plugin allows you to use a default set of meta tags via the `open-graph.default` option as well as use template specific sets by using the `open-graph.templates` option. Check out some examples:

```php
c::get('open-graph.default', [
    'title' => page()->title(),
    'url' => page()->url(),
    'site_name' => site()->title(),
    'type' => 'website'
]);

c::set('open-graph.templates', [
    'article' => [
        'type' => 'article',
        'image' => page()->cover()->url(),
        'attributes' => function($page) {
            return [
                // if strftime, use $page->date('%F')
                'published_time' => $page->date('c'),
                'modified_time' => $page->modified('c'),
            ];
        }
    ],
]);
```

When the current page has a specific Open Graph template defined at `open-graph.templates`, it will be merged and will override the default properties so you don't need to repeat yourself!

For more information on all the available open graph objects, check out the [specification](http://ogp.me).

## Roadmap
- [ ] Add image attributes (width, height, type)
- [ ] Add author profile
- [ ] Enable tags array

## Change Log
All notable changes to this project will be documented at: <https://github.com/pedroborges/kirby-open-graph/blob/master/changelog.md>

## License
Open Graph plugin is open-sourced software licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).

Copyright © 2017 Pedro Borges <oi@pedroborg.es>
