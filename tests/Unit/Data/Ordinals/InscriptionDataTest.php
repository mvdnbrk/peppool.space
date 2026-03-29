<?php

namespace Tests\Unit\Data\Ordinals;

use App\Data\Ordinals\InscriptionData;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InscriptionDataTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_with_null_address(): void
    {
        $data = [
            'id' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3i0',
            'number' => 17212333,
            'address' => null,
            'child_count' => 0,
            'children' => [],
            'content_type' => 'image/png',
            'effective_content_type' => 'image/png',
            'content_length' => 793,
            'delegate' => null,
            'fee' => 10210000,
            'height' => 956437,
            'value' => 100000,
            'parent_count' => 0,
            'parents' => [],
            'properties' => null,
            'satpoint' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3:0:0',
            'timestamp' => 1773570237,
            'next' => null,
            'previous' => null,
        ];

        $inscription = InscriptionData::from($data);

        $this->assertNull($inscription->address);
        $this->assertFalse($inscription->hasTitle());
        $this->assertFalse($inscription->hasTraits());
        $this->assertFalse($inscription->isDelegate());
        $this->assertNull($inscription->getTitle());
        $this->assertFalse($inscription->hasParents());
        $this->assertTrue($inscription->getParents()->isEmpty());
        $this->assertFalse($inscription->hasChildren());
        $this->assertTrue($inscription->getChildren()->isEmpty());
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $inscription->getTraits());
        $this->assertTrue($inscription->getTraits()->isEmpty());
    }

    #[Test]
    public function it_can_be_instantiated_with_null_next_or_previous(): void
    {
        $data = [
            'id' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3i0',
            'number' => 17212333,
            'address' => 'Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG',
            'child_count' => 0,
            'children' => [],
            'content_type' => 'image/png',
            'effective_content_type' => 'image/png',
            'content_length' => 793,
            'delegate' => '768e65ae997ab356aa512fee781fb276ffe08eb905778dfce54534299853a9a4i0',
            'fee' => 10210000,
            'height' => 956437,
            'value' => 100000,
            'parent_count' => 0,
            'parents' => [],
            'properties' => null,
            'satpoint' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3:0:0',
            'timestamp' => 1773570237,
            'next' => null,
            'previous' => null,
        ];

        $inscription = InscriptionData::from($data);

        $this->assertEquals($data['id'], $inscription->id);
        $this->assertTrue($inscription->isDelegate());
        $this->assertNull($inscription->next);
        $this->assertNull($inscription->previous);
    }

    #[Test]
    public function it_can_be_instantiated_with_the_users_provided_json(): void
    {
        $data = [
            'id' => '7d54dd7fecfe8d80e4f09c96cc047d994acbd84b2a05f9b1cbdc4a9b565de23ai0',
            'number' => 17256908,
            'address' => 'Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG',
            'childCount' => 1,
            'children' => ['child_id_456'],
            'contentType' => 'text/html;charset=utf-8',
            'effectiveContentType' => 'text/html;charset=utf-8',
            'contentLength' => 926,
            'fee' => 11690000,
            'height' => 960450,
            'value' => 100000,
            'parentCount' => 1,
            'parents' => ['parent_id_123'],
            'properties' => [
                'title' => 'Cat Head Blue',
                'traits' => [
                    'tribe' => 'cat',
                    'type' => 'head',
                    'color' => 'blue',
                ],
            ],
            'satpoint' => '7d54dd7fecfe8d80e4f09c96cc047d994acbd84b2a05f9b1cbdc4a9b565de23a:0:0',
            'timestamp' => 1773823195,
            'next' => 'c24aec607c8df7eee6bf9fc4b724ac6821ccf0fd134fb5a2811bb3a9fedd3afai0',
            'previous' => '152e46160bd80ddeba69e2e29cbf84b3353c18ee3362322de674e2ca84f748f7i0',
        ];

        $inscription = InscriptionData::from($data);

        $this->assertEquals($data['id'], $inscription->id);
        $this->assertEquals($data['contentType'], $inscription->content_type);
        $this->assertEquals($data['contentLength'], $inscription->content_length);
        $this->assertEquals($data['next'], $inscription->next);
        $this->assertEquals($data['previous'], $inscription->previous);
        $this->assertEquals('Cat Head Blue', $inscription->properties['title']);
        $this->assertTrue($inscription->hasTitle());
        $this->assertTrue($inscription->hasTraits());
        $this->assertFalse($inscription->isDelegate());
        $this->assertTrue($inscription->hasParents());
        $this->assertEquals('parent_id_123', $inscription->getParents()->first());
        $this->assertTrue($inscription->hasChildren());
        $this->assertEquals('child_id_456', $inscription->getChildren()->first());
        $this->assertEquals(1, $inscription->getChildCount());
        $this->assertEquals('Cat Head Blue', $inscription->getTitle());
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $inscription->getTraits());
        $this->assertEquals('cat', $inscription->getTraits()->get('tribe'));
    }
}
