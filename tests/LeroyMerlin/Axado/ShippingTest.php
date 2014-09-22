<?php
namespace Axado;

use TestCase;
use Mockery as m;

class ShippingTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldRenderPropertlyInJson()
    {
        // Set
        $request   = m::mock('Axado\Request');
        $volume    = m::mock('Axado\VolumeInterface');
        $formatter = m::mock('Axado\JsonFormatter');
        $shipping  = new Shipping($request, $formatter);
        $expect    = "{json: 'withproducts'}";

        $shipping->addVolume($volume);

        // Expect
        $formatter->shouldReceive('setInstance')
            ->once()
            ->with($shipping);

        $formatter->shouldReceive('format')
            ->once()
            ->andReturn($expect);

        // Assert
        $result = $shipping->toJson();

        $this->assertEquals($expect, $result);
    }

    public function testShouldAddAVolume()
    {
        $request   = m::mock('Axado\Request');
        $volume    = m::mock('Axado\VolumeInterface');
        $formatter = m::mock('Axado\JsonFormatter');
        $shipping  = new Shipping($request, $formatter);

        $shipping->addVolume($volume);

        $expect = $shipping->allVolumes();

        $this->assertEquals([$volume], $expect);
    }

    public function testShouldCleanAllVolumes()
    {
        $request   = m::mock('Axado\Request');
        $volume    = m::mock('Axado\VolumeInterface');
        $formatter = m::mock('Axado\JsonFormatter');
        $shipping  = new Shipping($request, $formatter);

        $shipping->addVolume($volume);

        $shipping->clearVolumes();

        $this->assertEquals([], $shipping->allVolumes());
    }
}
