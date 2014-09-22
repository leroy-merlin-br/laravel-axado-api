<?php
namespace Axado;

use TestCase;
use Mockery as m;

class JsonFormatterTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldFormatPropertlyTheInstanceGiven()
    {
        $instance   = m::mock('Axado\Shipping');
        $formatter  = new JsonFormatter;
        $attributes = ['preco' => 10.5, 'unidade' => 10];
        $volume     = m::mock('Axado\VolumeInterface');

        // Expect
        $instance->shouldReceive('getAllAttributes')
            ->once()
            ->andReturn($attributes);

        $volume->shouldReceive('volumeToJson')
            ->once()
            ->andReturn('{ preco: 10.50, unidade: 1 }');

        $instance->shouldReceive('allVolumes')
            ->once()
            ->andReturn([ $volume ]);

        $formatter->setInstance($instance);
        $result = $formatter->format();

        $this->assertEquals('{"preco":10.5,"unidade":10,"volumes":["{ preco: 10.50, unidade: 1 }"]}', $result);
    }
}
