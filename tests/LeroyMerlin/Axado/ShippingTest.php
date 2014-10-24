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

    /**
     * @expectedException Axado\ShippingException
     * @return [type] [description]
     */
    public function testShouldThrowAxadoExceptionWhenTheShippingIsNotValid()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[isValid]');

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect

        $shipping->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        // Act
        $shipping->quotations();
    }

    public function testShouldReturnTheGetCostsPropertly()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[firstQuotation]');
        $quotation = m::mock('Axado\Quotation');
        $expected  = 10.5;
        $quotation->quotation_price = $expected;

        $shipping->shouldAllowMockingProtectedMethods();

        $quotation->shouldReceive('getCosts')
            ->once()
            ->andReturn($expected);

        // Expect
        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn($quotation);

        // Act
        $result = $shipping->getCosts();

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testShouldReturnNullGettingCostsIfHasNotQuotation()
    {
        // Set
        $shipping = m::mock('Axado\Shipping[firstQuotation]');
        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn([]);

        // Act
        $result = $shipping->getCosts();

        // Assert
        $this->assertNull($result);
    }

    public function testShouldReturnTheGetDeadlinePropertly()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[firstQuotation]');
        $quotation = m::mock('Axado\Quotation');
        $expected  = 4;
        $quotation->deadline = $expected;

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn($quotation);

        $quotation->shouldReceive('getDeadline')
            ->once()
            ->andReturn($expected);

        // Act
        $result = $shipping->getDeadline();

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testShouldReturnNullGettingDeadlineIfHasNotQuotation()
    {
        // Set
        $shipping = m::mock('Axado\Shipping[firstQuotation]');

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('firstQuotation')
            ->once()
            ->andReturn([]);

        // Act
        $result = $shipping->getDeadline();

        // Assert
        $this->assertNull($result);
    }


    public function testShouldGetFirstQuotation()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[quotations]');
        $quotation = m::mock('Axado\Quotation');

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('quotations')
            ->once()
            ->andReturn([$quotation]);

        // Act
        $result = $shipping->firstQuotation();

        // Assert
        $this->assertEquals($quotation, $result);
    }

    public function testShouldReturnNullIfHasNoQuotation()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[quotations]');
        $quotation = m::mock('Axado\Quotation');

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('quotations')
            ->once()
            ->andReturn([]);

        // Act
        $result = $this->callProtected($shipping, 'firstQuotation');

        // Assert
        $this->assertNull($result);
    }

    public function testShouldReturnQuotations()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[newRequest,isValid]');
        $token     = '123466';
        $request   = m::mock('Axado\Request[consultShipping,quotations]', [$token]);
        $quotation = m::mock('Axado\Quotation');
        $response  = m::mock('Axado\Response');

        $shipping::$token = $token;

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $request->shouldReceive('consultShipping')
            ->with('[]')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('quotations')
            ->twice()
            ->andReturn([]);

        $response->shouldReceive('getQuotationToken')
            ->once()
            ->andReturn("1231203das01");

        $shipping->shouldReceive('newRequest')
            ->once()
            ->with($token)
            ->andReturn($request);

        $shipping->shouldReceive('isValid')
            ->twice()
            ->andReturn(true);

        // Act
        $shipping->quotations();
        $shipping->quotations();
    }

    public function testShouldReturnANewInstanceOfRequest()
    {
        // Set
        $shipping = new Shipping;

        // Act
        $result = $this->callProtected($shipping, 'newRequest', '12345');

        // Assert
        $this->assertTrue($result instanceof \Axado\Request);
    }

    public function testShouldVerifyIfThisInstanceIsValid()
    {
        // Set
        $shipping = new Shipping;
        $volume   = m::mock('Axado\Volume\VolumeInterface');

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setTotalPrice('21.2');
        $shipping->setAditionalDays('12');
        $shipping->setAditionalPrice('12.6');

        $shipping->addVolume($volume);

        // Act
        $result = $this->callProtected($shipping, 'isValid');

        // Assert
        $this->assertTrue($result);
    }

    public function testShouldInvalidWhenVolumeIsEmpty()
    {
        // Set
        $shipping = new Shipping;

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setTotalPrice('21.2');
        $shipping->setAditionalDays('12');
        $shipping->setAditionalPrice('12.6');

        // Act
        $result = $this->callProtected($shipping, 'isValid');

        // Assert
        $this->assertFalse($result);
    }

    public function testShouldInvalidWhenIsEmpty()
    {
        // Set
        $shipping = new Shipping;
        $volume   = m::mock('Axado\Volume\VolumeInterface');

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setAditionalDays('12');
        $shipping->setAditionalPrice('12.6');

        $shipping->addVolume($volume);

        // Act
        $result = $this->callProtected($shipping, 'isValid');

        // Assert
        $this->assertFalse($result);
    }

    public function testShouldFlagAQuotationAsContracted()
    {
        // Set
        $shipping = m::mock('Axado\Shipping[newRequest]');
        $request  = m::mock('Axado\Request[flagAsContracted]', ['1010']);

        $shipping->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('newRequest')
            ->once()
            ->andReturn($request);

        $request->shouldReceive('flagAsContracted')
            ->with($shipping, '')
            ->once();

        // Act
        $shipping->flagAsContracted();
    }

    public function testShouldReturnGetQuotationElected()
    {
        // Set
        $shipping  = m::mock('Axado\Shipping[quotations]');
        $quotation = m::mock('Axado\Quotation');

        // Expect
        $shipping->shouldReceive('quotations')
            ->once()
            ->andReturn([$quotation]);

        // Act
        $this->callProtected($shipping, 'firstQuotation');

        // Assert
        $this->assertEquals($quotation, $shipping->getQuotationElected());
    }
}
