<?php

use PedroBorges\MetaTags\MetaTags;
use PHPUnit\Framework\TestCase;

class MetaTagsTest extends TestCase
{
    public function setUp()
    {
        $this->head = new MetaTags;
    }

    public function testTitleTag()
    {
        $tag = $this->head->title('"Title tag" test');

        $this->assertEquals('<title>&quot;Title tag&quot; test</title>', $tag);
    }

    public function testArrayOfTags()
    {
        $tag1 = $this->head->link('alternate', [
            'hreflang' => 'pt-br',
            'href' => 'https://br.pedroborg.es'
        ]);

        $tag2 = $this->head->link('alternate', [
            'hreflang' => 'en-us',
            'href' => 'https://en.pedroborg.es'
        ]);

        $html = $this->head->render();

        $expectedHtml = <<<'EOD'
    <link rel="alternate" hreflang="pt-br" href="https://br.pedroborg.es">
    <link rel="alternate" hreflang="en-us" href="https://en.pedroborg.es">

EOD;

        $this->assertEquals($expectedHtml, $html);
    }

    public function testLinkTag()
    {
        $tag = $this->head->link('canonical', 'https://pedroborg.es');
        $alternate = $this->head->link('alternate', [
            'hreflang' => 'pt-br',
            'href' => 'https://br.pedroborg.es'
        ]);

        $this->assertEquals('<link rel="canonical" href="https://pedroborg.es">', $tag);
        $this->assertEquals('<link rel="alternate" hreflang="pt-br" href="https://br.pedroborg.es">', $alternate);
    }

    public function testMetaTag()
    {
        $tag = $this->head->meta('description', 'Meta tag test');
        $encoded = $this->head->meta('description', '"Meta tag" test');

        $this->assertEquals('<meta name="description" content="Meta tag test">', $tag);
        $this->assertEquals('<meta name="description" content="&quot;Meta tag&quot; test">', $encoded);
    }

    public function testOpenGraphTag()
    {
        $tag = $this->head->og('title', 'Open Graph test');
        $preffixed = $this->head->og('og:title', 'Open Graph test', false);

        $this->assertEquals('<meta property="og:title" content="Open Graph test">', $tag);
        $this->assertEquals('<meta property="og:title" content="Open Graph test">', $preffixed);
    }

    public function testTwitterCardTag()
    {
        $tag = $this->head->twitter('card', 'summary');
        $preffixed = $this->head->twitter('twitter:card', 'summary', false);

        $this->assertEquals('<meta name="twitter:card" content="summary">', $tag);
        $this->assertEquals('<meta name="twitter:card" content="summary">', $preffixed);
    }

    public function testRendering()
    {
        $this->head->link('canonical', 'https://pedroborg.es');
        $this->head->twitter('card', 'summary');
        $this->head->title('<title> tag test');
        $this->head->og('title', 'Open Graph test');
        $this->head->meta('description', '"Meta Tags" test');

        $html = $this->head->render();

        $expectedHtml = <<<'EOD'
    <title>&lt;title&gt; tag test</title>
    <meta name="description" content="&quot;Meta Tags&quot; test">
    <meta property="og:title" content="Open Graph test">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="https://pedroborg.es">

EOD;

        $this->assertEquals($expectedHtml, $html);
    }
}
