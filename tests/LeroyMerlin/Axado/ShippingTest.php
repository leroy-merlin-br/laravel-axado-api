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
        $result = $this->callProtected($shipping, 'toJson');
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

    public function testShouldValidShippingBeforeRequesting()
    {
        $this->assertTrue(false);
    }

    public function testShouldReturnTheGetCostsPropertly()
    {
        $shipping = m::mock('Axado\Shipping[firstQuotation]');
        $quotation = m::mock('Axado\Quotation');
        $expected = 10.5;
        $quotation->quotation_price = $expected;

        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn($quotation);

        $result = $shipping->getCosts();

        $this->assertEquals($expected, $result);
    }

    public function testShouldReturnNullGettingCostsIfHasNotQuotation()
    {
        $shipping = m::mock('Axado\Shipping[firstQuotation]');

        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn([]);

        $result = $shipping->getCosts();

        $this->assertNull($result);
    }

    public function testShouldReturnTheGetDeadlinePropertly()
    {
        $shipping = m::mock('Axado\Shipping[firstQuotation]');
        $quotation = m::mock('Axado\Quotation');
        $expected = 4;
        $quotation->deadline = $expected;

        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn($quotation);

        $result = $shipping->getDeadline();

        $this->assertEquals($expected, $result);
    }

    public function testShouldReturnNullGettingDeadlineIfHasNotQuotation()
    {
        $shipping = m::mock('Axado\Shipping[firstQuotation]');

        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn([]);

        $result = $shipping->getDeadline();

        $this->assertNull($result);
    }


    public function testShouldGetFirstQuotation()
    {
        $shipping = m::mock('Axado\Shipping[quotations]');
        $quotation = m::mock('Axado\Quotation');

        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->shouldReceive('quotations')
            ->once()
            ->andReturn([$quotation]);

        $result = $this->callProtected($shipping, 'firstQuotation');

        $this->assertEquals($quotation, $result);
    }

    public function testShouldReturnNullIfHasNoQuotation()
    {
        $shipping = m::mock('Axado\Shipping[quotations]');
        $quotation = m::mock('Axado\Quotation');

        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->shouldReceive('quotations')
            ->once()
            ->andReturn([]);

        $result = $this->callProtected($shipping, 'firstQuotation');

        $this->assertNull($result);
    }

    public function testShouldReturnQuotations()
    {
        $shipping  = m::mock('Axado\Shipping[newRequest]');
        $token     = '123466';
        $request   = m::mock('Axado\Request[consultShipping,quotations]', [$token]);
        $quotation = m::mock('Axado\Quotation');
        $response  = m::mock('Axado\Response');

        $shipping::$token = $token;

        $shipping->shouldAllowMockingProtectedMethods();

        $request->shouldReceive('consultShipping')
            ->with('[]')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('quotations')
            ->twice()
            ->andReturn([]);

        $shipping->shouldReceive('newRequest')
            ->once()
            ->with($token)
            ->andReturn($request);

        $shipping->quotations();
        $shipping->quotations();
    }

    public function testShouldReturnANewInstanceOfRequest()
    {
        $shipping = new Shipping;

        $result = $this->callProtected($shipping, 'newRequest', '12345');

        $this->assertTrue($result instanceof \Axado\Request);
    }
}
