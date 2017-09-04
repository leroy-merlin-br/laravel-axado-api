<?php
namespace Axado\Formatter;

use Axado\Shipping;
use Axado\TestCase;
use Axado\Volume\VolumeInterface;
use Mockery as m;

class JsonFormatterTest extends TestCase
{
    public function testShouldFormatProperlyTheShippingInstanceGiven()
    {
        // Set
        $formatter = new JsonFormatter();
        $instance = m::mock(Shipping::class);
        $formatter->setInstance($instance);

        $volume = m::mock(VolumeInterface::class);
        $attributes = ['preco' => 10.5, 'unidade' => 10];

        // Expectations
        $instance->shouldReceive('getAttributes')
            ->withNoArgs()
            ->once()
            ->andReturn($attributes);

        $volume->shouldReceive('volumeToArray')
            ->withNoArgs()
            ->once()
            ->andReturn(['preco' => 10.50, 'unidade' => 1]);

        $instance->shouldReceive('allVolumes')
            ->withNoArgs()
            ->once()
            ->andReturn([$volume]);

        // Actions
        $result = $formatter->format();

        // Assertions
        $this->assertJson(
            '{"preco":10.5,"unidade":10,"volumes":[{"preco":10.5,"unidade":1}]}',
            $result);
    }
}
