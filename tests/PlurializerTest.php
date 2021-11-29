<?php

namespace Jugid\Finflector\Tests;

use InvalidArgumentException;
use Jugid\Finflector\Pluralizer;
use PHPUnit\Framework\TestCase;

class PooxStyleTest extends TestCase {

    private Pluralizer $plurializer;

    public function setUp() : void {
        $this->plurializer = new Pluralizer();
    }

    public function testShouldGetPluralOfSpecialWord() {
        $plurial = $this->plurializer->pluralize('oeil');
        $this->assertSame('yeux', $plurial);
    }

    public function testShouldGetRightPlurals() {
        $words = [
            'bus' => 'bus', 
            'bras' => 'bras', 
            'colis' => 'colis', 
            'héros' => 'héros', 
            'joue' => 'joues',
            'bâteau' => 'bâteaux', 
            'château' => 'châteaux',
            'oiseau' => 'oiseaux',
            'cheveu' => 'cheveux', 
            'lieu' => 'lieux',
            'animal' => 'animaux', 
            'cheval' => 'chevaux',
            'bijou' => 'bijoux', 
            'genou' => 'genoux', 
            'joujou' => 'joujoux',
            'autres' => 'autres', 
            'châteaux' => 'châteaux', 
            'héros' => 'héros', 
            'lieux' => 'lieux', 
            'animaux' => 'animaux',
            'le site web' => 'les sites web',
            'l\'eau' => 'les eaux',
            'du vin' => 'des vins',
            'Du vin' => 'Des vins',
            'Un animal' => 'Des animaux'
        ];

        foreach($words as $word => $plural) {
            $this->assertSame($plural, $this->plurializer->pluralize($word));
        }
    }

}