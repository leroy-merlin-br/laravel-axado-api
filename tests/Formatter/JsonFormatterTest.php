<?php
namespace Axado\Formatter;

use Mockery as m;
use TestCase;

class JsonFormatterTest extends TestCase
{
    public function testShouldFormatPropertlyTheShippingInstanceGiven()
    {
        // Set
        $formatter  = new JsonFormatter;
        $instance   = m::mock('Axado\Shipping');
        $volume     = m::mock('Axado\Volume\VolumeInterface');
        $attributes = ['preco' => 10.5, 'unidade' => 10];

        // Expect
        $instance->shouldReceive('getAttributes')
            ->once()
            ->andReturn($attributes);

        $volume->shouldReceive('volumeToArray')
            ->once()
            ->andReturn(['preco' => 10.50, 'unidade' => 1]);

        $instance->shouldReceive('allVolumes')
            ->once()
            ->andReturn([ $volume ]);

        // Act
        $formatter->setInstance($instance);
        $result = $formatter->format();

        // Assert
        $this->assertEquals(
            '{"preco":10.5,"unidade":10,"volumes":[{"preco":10.5,"unidade":1}]}',
            $result
        );
    }
}
