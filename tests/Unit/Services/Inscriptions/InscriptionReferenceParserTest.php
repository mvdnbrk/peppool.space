<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Inscriptions;

use App\Services\Inscriptions\InscriptionReferenceParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InscriptionReferenceParserTest extends TestCase
{
    private InscriptionReferenceParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new InscriptionReferenceParser;
    }

    #[Test]
    public function it_extracts_inscription_id_from_js_import(): void
    {
        $content = "import { OrdClient } from '/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3';";

        $result = $this->parser->parse('text/html', $content);

        $this->assertSame(['031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3'], $result);
    }

    #[Test]
    public function it_extracts_inscription_id_from_html_src(): void
    {
        $content = '<script src="/content/4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0"></script>';

        $result = $this->parser->parse('text/html', $content);

        $this->assertSame(['4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0'], $result);
    }

    #[Test]
    public function it_extracts_inscription_id_from_single_quoted_src(): void
    {
        $content = "<img src='/content/4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0'>";

        $result = $this->parser->parse('text/html', $content);

        $this->assertSame(['4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0'], $result);
    }

    #[Test]
    public function it_extracts_multiple_unique_ids(): void
    {
        $content = <<<'HTML'
        <script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3"></script>
        <img src="/content/4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0">
        HTML;

        $result = $this->parser->parse('text/html', $content);

        $this->assertCount(2, $result);
        $this->assertContains('031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3', $result);
        $this->assertContains('4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0', $result);
    }

    #[Test]
    public function it_deduplicates_repeated_ids(): void
    {
        $content = <<<'HTML'
        <script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3"></script>
        <script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3"></script>
        HTML;

        $result = $this->parser->parse('text/html', $content);

        $this->assertSame(['031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3'], $result);
    }

    #[Test]
    public function it_returns_empty_for_null_content(): void
    {
        $this->assertSame([], $this->parser->parse('text/html', null));
    }

    #[Test]
    public function it_returns_empty_for_empty_content(): void
    {
        $this->assertSame([], $this->parser->parse('text/html', ''));
    }

    #[Test]
    public function it_returns_empty_for_non_text_content_type(): void
    {
        $content = '<script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3"></script>';

        $this->assertSame([], $this->parser->parse('image/png', $content));
    }

    #[Test]
    public function it_returns_empty_for_null_content_type(): void
    {
        $content = '<script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3"></script>';

        $this->assertSame([], $this->parser->parse(null, $content));
    }

    #[Test]
    public function it_returns_empty_when_no_references_found(): void
    {
        $this->assertSame([], $this->parser->parse('text/html', '<p>No references here</p>'));
    }

    #[Test]
    public function it_handles_text_plain_content_type(): void
    {
        $content = '/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3';

        $result = $this->parser->parse('text/plain', $content);

        $this->assertSame(['031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3'], $result);
    }

    #[Test]
    public function it_handles_content_type_with_charset(): void
    {
        $content = '<script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3"></script>';

        $result = $this->parser->parse('text/html;charset=utf-8', $content);

        $this->assertSame(['031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3'], $result);
    }

    #[Test]
    public function it_extracts_from_css_url(): void
    {
        $content = "background: url('/content/4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0');";

        $result = $this->parser->parse('text/css', $content);

        $this->assertSame(['4bb903201dd37f718018d77835e71d9b519cd7ee5f66eac26f631b2afea7d9cei0'], $result);
    }

    #[Test]
    public function it_extracts_from_backtick_template_literal(): void
    {
        $content = 'const url = `/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3`;';

        $result = $this->parser->parse('text/javascript', $content);

        $this->assertSame(['031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi3'], $result);
    }

    #[Test]
    public function it_handles_index_greater_than_zero(): void
    {
        $content = '<script src="/content/031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi15"></script>';

        $result = $this->parser->parse('text/html', $content);

        $this->assertSame(['031d60cebab0d0e96f15ac512dbe3953ff1586d8e86f2f35e22bac76519bf0dbi15'], $result);
    }
}
