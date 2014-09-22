<?php
namespace Axado;

use TestCase;
use Axado\Shipping;
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
        $volume    = m::mock('Axado\Volume\VolumeInterface');
        $formatter = m::mock('Axado\Formatter\JsonFormatter');
        $shipping  = new Shipping($formatter);
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
        // Set
        $volume    = m::mock('Axado\Volume\VolumeInterface');
        $shipping  = new Shipping;

        // act
        $shipping->addVolume($volume);

        // Assert
        $expect = $shipping->allVolumes();
        $this->assertEquals([$volume], $expect);
    }

    public function testShouldCleanAllVolumes()
    {
        // Set
        $volume    = m::mock('Axado\Volume\VolumeInterface');
        $shipping  = new Shipping;

        // Act
        $shipping->addVolume($volume);
        $shipping->clearVolumes();

        // Assert
        $this->assertEquals([], $shipping->allVolumes());
    }

    public function testShouldPrepareRightAttributes()
    {
        // Set
        $shipping = new Shipping;

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setTotalPrice('21.2');
        $shipping->setAditionalDays('12');
        $shipping->setAditionalPrice('12.6');

        $expected = [
            "cep_origem"       => '123123',
            "cep_destino"      => '01010',
            "valor_notafiscal" => 21.2,
            "prazo_adicional"  => 12,
            "preco_adicional"  => 12.6
        ];

        // Act
        $result = $shipping->getAttributes();

        // Assert
        $this->assertEquals($result, $expected);
    }
}
