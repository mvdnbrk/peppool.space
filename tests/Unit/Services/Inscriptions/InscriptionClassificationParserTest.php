<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Inscriptions;

use App\Models\Inscription;
use App\Services\Inscriptions\InscriptionClassificationParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InscriptionClassificationParserTest extends TestCase
{
    private InscriptionClassificationParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new InscriptionClassificationParser;
    }

    // --- Pepemap ---

    #[Test]
    public function it_detects_valid_pepemap(): void
    {
        $result = $this->parser->parse('text/plain', '12345.pepemap');
        $this->assertTrue(($result['flags'] & Inscription::FLAG_BITMAP) !== 0);
    }

    #[Test]
    public function it_detects_pepemap_with_whitespace(): void
    {
        $result = $this->parser->parse('text/plain', " 12345.pepemap\n");
        $this->assertTrue(($result['flags'] & Inscription::FLAG_BITMAP) !== 0);
    }

    #[Test]
    public function it_detects_pepemap_with_charset_content_type(): void
    {
        $result = $this->parser->parse('text/plain;charset=utf-8', '0.pepemap');
        $this->assertTrue(($result['flags'] & Inscription::FLAG_BITMAP) !== 0);
    }

    #[Test]
    public function it_rejects_pepemap_with_wrong_content_type(): void
    {
        $this->assertFalse($this->parser->isPepemap('application/json', '12345.pepemap'));
    }

    #[Test]
    public function it_rejects_pepemap_with_letters(): void
    {
        $this->assertFalse($this->parser->isPepemap('text/plain', 'abc.pepemap'));
    }

    #[Test]
    public function it_rejects_pepemap_with_extra_text(): void
    {
        $this->assertFalse($this->parser->isPepemap('text/plain', '123.pepemap extra'));
    }

    #[Test]
    public function it_rejects_bitmap_suffix(): void
    {
        $this->assertFalse($this->parser->isPepemap('text/plain', '123.bitmap'));
    }

    // --- PRC-20 Deploy ---

    #[Test]
    public function it_validates_valid_deploy(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
            'max' => '21000000',
            'lim' => '1000',
        ]);

        $result = $this->parser->parse('application/json', $content);
        $this->assertTrue(($result['flags'] & Inscription::FLAG_PRC20) !== 0);
        $this->assertTrue($result['prc20']['valid']);
        $this->assertSame('deploy', $result['prc20']['op']);
        $this->assertSame('pepe', $result['prc20']['tick']);
        $this->assertEmpty($result['prc20']['errors']);
    }

    #[Test]
    public function it_validates_deploy_without_optional_fields(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
            'max' => '21000000',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_validates_deploy_with_decimals(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
            'max' => '21000000',
            'dec' => '8',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_rejects_deploy_with_decimals_exceeding_18(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
            'max' => '21000000',
            'dec' => '19',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
        $this->assertContains('Decimals must be between 0 and 18', $result['errors']);
    }

    #[Test]
    public function it_rejects_deploy_without_max(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
        $this->assertContains('Missing or invalid max supply', $result['errors']);
    }

    #[Test]
    public function it_rejects_deploy_with_max_exceeding_uint64(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
            'max' => '18446744073709551616', // uint64_max + 1
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
        $this->assertContains('Max supply must be a positive integer not exceeding uint64', $result['errors']);
    }

    #[Test]
    public function it_accepts_deploy_with_max_at_uint64_max(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'deploy',
            'tick' => 'pepe',
            'max' => '18446744073709551615',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
    }

    // --- PRC-20 Mint ---

    #[Test]
    public function it_validates_valid_mint(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => '1000',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
        $this->assertSame('mint', $result['op']);
    }

    #[Test]
    public function it_rejects_mint_without_amount(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
        $this->assertContains('Missing or invalid amount', $result['errors']);
    }

    #[Test]
    public function it_rejects_mint_with_amount_exceeding_uint128(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => '340282366920938463463374607431768211456', // uint128_max + 1
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
    }

    // --- PRC-20 Transfer ---

    #[Test]
    public function it_validates_valid_transfer(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'transfer',
            'tick' => 'pepe',
            'amt' => '100',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
        $this->assertSame('transfer', $result['op']);
    }

    #[Test]
    public function it_validates_transfer_with_optional_to_and_fee(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'transfer',
            'tick' => 'pepe',
            'amt' => '100',
            'to' => 'PsomeAddress',
            'fee' => '10',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
    }

    // --- PRC-20 General Validation ---

    #[Test]
    public function it_rejects_tick_with_wrong_length(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'ab',
            'amt' => '100',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
        $this->assertContains('Tick must be exactly 4 characters', $result['errors']);
    }

    #[Test]
    public function it_rejects_invalid_op(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'burn',
            'tick' => 'pepe',
            'amt' => '100',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
        $this->assertContains('Invalid or missing op', $result['errors']);
    }

    #[Test]
    public function it_handles_case_insensitive_protocol(): void
    {
        $content = json_encode([
            'p' => 'PRC-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => '1000',
        ]);

        $result = $this->parser->parsePrc20('text/plain', $content);
        $this->assertNotNull($result);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_handles_case_insensitive_op(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'MINT',
            'tick' => 'pepe',
            'amt' => '1000',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_returns_null_for_non_prc20_json(): void
    {
        $content = json_encode(['name' => 'test']);
        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_for_invalid_json(): void
    {
        $result = $this->parser->parsePrc20('application/json', 'not json');
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_for_image_content_type(): void
    {
        $content = json_encode(['p' => 'prc-20', 'op' => 'mint', 'tick' => 'pepe', 'amt' => '100']);
        $result = $this->parser->parsePrc20('image/png', $content);
        $this->assertNull($result);
    }

    #[Test]
    public function it_rejects_zero_amount(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => '0',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
    }

    #[Test]
    public function it_rejects_negative_amount(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => '-100',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
    }

    #[Test]
    public function it_rejects_non_numeric_amount(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => 'abc',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertFalse($result['valid']);
    }

    // --- Parse method ---

    #[Test]
    public function it_sets_analyzed_flag(): void
    {
        $result = $this->parser->parse('text/plain', 'hello');
        $this->assertTrue(($result['flags'] & Inscription::FLAG_ANALYZED) !== 0);
    }

    #[Test]
    public function it_returns_zero_flags_for_null_content(): void
    {
        $result = $this->parser->parse('text/plain', null);
        $this->assertSame(0, $result['flags']);
        $this->assertNull($result['prc20']);
    }

    #[Test]
    public function it_returns_zero_flags_for_empty_content(): void
    {
        $result = $this->parser->parse('text/plain', '');
        $this->assertSame(0, $result['flags']);
    }

    #[Test]
    public function it_can_detect_both_pepemap_and_prc20_independently(): void
    {
        // A pepemap is never PRC-20
        $result = $this->parser->parse('text/plain', '500.pepemap');
        $this->assertTrue(($result['flags'] & Inscription::FLAG_BITMAP) !== 0);
        $this->assertNull($result['prc20']);
    }

    #[Test]
    public function it_preserves_tick_case(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'PEPE',
            'amt' => '1000',
        ]);

        $result = $this->parser->parsePrc20('application/json', $content);
        $this->assertSame('PEPE', $result['tick']);
    }

    #[Test]
    public function it_accepts_text_plain_with_charset_for_prc20(): void
    {
        $content = json_encode([
            'p' => 'prc-20',
            'op' => 'mint',
            'tick' => 'pepe',
            'amt' => '1000',
        ]);

        $result = $this->parser->parsePrc20('text/plain;charset=utf-8', $content);
        $this->assertNotNull($result);
        $this->assertTrue($result['valid']);
    }
}
