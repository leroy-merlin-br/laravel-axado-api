<?php
namespace Axado;

use TestCase;
use Mockery as m;
use \Axado\Formatter\JsonFormatter;

class JsonFormatterTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldFormatPropertlyTheInstanceGiven()
    {
        $formatter  = new JsonFormatter;
        $instance   = m::mock('Axado\Shipping');
        $volume     = m::mock('Axado\Volume\VolumeInterface');
        $attributes = ['preco' => 10.5, 'unidade' => 10];

        // Expect
        $instance->shouldReceive('getAllAttributes')
            ->once()
            ->andReturn($attributes);

        $volume->shouldReceive('volumeToArray')
            ->once()
            ->andReturn(['preco' => 10.50, 'unidade' => 1]);

        $instance->shouldReceive('allVolumes')
            ->once()
            ->andReturn([ $volume ]);

        $formatter->setInstance($instance);
        $result = $formatter->format();

        $this->assertEquals(
            '{"preco":10.5,"unidade":10,"volumes":[{"preco":10.5,"unidade":1}]}',
            $result
        );
    }
}
