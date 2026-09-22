<?php

namespace App\Tests\Unit\Block;

use App\Block\Type\HeroTremaBlock;
use PHPUnit\Framework\TestCase;

class HeroTremaBlockTest extends TestCase
{
    public function testKeyAndTemplate(): void
    {
        $block = new HeroTremaBlock();

        $this->assertSame('hero_trema', $block->getKey());
        $this->assertSame('blocks/hero_trema.html.twig', $block->getTemplate());
    }

    public function testDefaultDataCoversEveryEditableField(): void
    {
        $defaults = (new HeroTremaBlock())->getDefaultData();

        foreach (['wordmark', 'label_left', 'label_center', 'statement', 'text',
            'primary_label', 'primary_link', 'secondary_label', 'secondary_link',
            'stat_value', 'stat_label', 'stat_link'] as $field) {
            $this->assertArrayHasKey($field, $defaults);
        }

        // Le logotype par défaut porte le « ï » qui déclenche le tréma
        $this->assertStringContainsString('ï', $defaults['wordmark']);
    }
}
