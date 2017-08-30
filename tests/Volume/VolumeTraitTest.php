<?php
namespace Axado\Volume;

use TestCase;

class VolumeTraitTest extends TestCase
{
    public function testShouldGetVolumeArray()
    {
        // Set
        $volume = new class () implements VolumeInterface
        {
            use VolumeTrait;

            public function getSku()
            {
                return '1';
            }

            public function getQuantity()
            {
                return 2;
            }

            public function getPriceUnit()
            {
                return 3.0;
            }

            public function getHeight()
            {
                return 4;
            }

            public function getLength()
            {
                return 5;
            }

            public function getWidth()
            {
                return 6;
            }

            public function getWeight()
            {
                return 7;
            }
        };

        $expected = [
            'sku' => '1',
            'quantidade' => 2,
            'preco' => 3.0,
            'altura' => 4,
            'comprimento' => 5,
            'largura' => 6,
            'peso' => 7,
        ];

        // Actions
        $result = $volume->volumeToArray();

        // Assertions
        $this->assertSame($expected, $result);
    }
}
