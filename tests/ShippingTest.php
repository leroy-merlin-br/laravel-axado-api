<?php
namespace Axado;

use Axado\Exception\DestinationNotFoundException;
use Axado\Exception\QuotationNotFoundException;
use Axado\Exception\ShippingException;
use Axado\Formatter\FormatterInterface;
use Axado\Formatter\JsonFormatter;
use Axado\Volume\VolumeInterface;
use Mockery as m;

class ShippingTest extends TestCase
{
    public function testShouldRenderProperlyInJson()
    {
        // Set
        $volume = m::mock(VolumeInterface::class);
        $formatter = m::mock(JsonFormatter::class);
        $shipping = new Shipping($formatter);
        $expect = "{json: 'withproducts'}";

        $shipping->addVolume($volume);

        // Expectations
        $formatter->shouldReceive('setInstance')
            ->with($shipping)
            ->once();

        $formatter->shouldReceive('format')
            ->withNoArgs()
            ->once()
            ->andReturn($expect);

        // Actions
        $result = $this->callProtected($shipping, 'toJson');

        // Assertions
        $this->assertSame($expect, $result);
    }

    public function testShouldAddAVolume()
    {
        // Set
        $volume = m::mock(VolumeInterface::class);
        $shipping = new Shipping();

        // Actions
        $shipping->addVolume($volume);
        $result = $shipping->allVolumes();

        // Assertions
        $this->assertSame([$volume], $result);
    }

    public function testShouldCleanAllVolumes()
    {
        // Set
        $volume = m::mock(VolumeInterface::class);
        $shipping = new Shipping();

        // Actions
        $shipping->addVolume($volume);
        $shipping->clearVolumes();
        $result = $shipping->allVolumes();

        // Assertions
        $this->assertSame([], $result);
    }

    public function testShouldPrepareRightAttributes()
    {
        // Set
        $shipping = new Shipping();

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setTotalPrice('21.2');
        $shipping->setAdditionalDays('12');
        $shipping->setAdditionalPrice('12.6');

        $expected = [
            'cep_origem' => '123123',
            'cep_destino' => '01010',
            'valor_notafiscal' => 21.2,
            'preco_adicional' => '12.6',
            'prazo_adicional' => 12,
        ];

        // Actions
        $result = $shipping->getAttributes();

        // Assertions
        $this->assertSame($result, $expected);
    }

    public function testShouldThrowAxadoExceptionWhenTheShippingIsNotValid()
    {
        // Set
        $shipping = m::mock(Shipping::class . '[isValid]');
        $shipping->shouldAllowMockingProtectedMethods();

        // Expectations
        $shipping->shouldReceive('isValid')
            ->withNoArgs()
            ->once()
            ->andReturn(false);

        $this->expectException(ShippingException::class);
        $this->expectExceptionMessage('This shipping was not filled correctly');

        // Actions
        $shipping->quotations();
    }

    public function testShouldReturnTheGetCostsProperly()
    {
        // Set
        $shipping = m::mock(Shipping::class . '[firstQuotation]');
        $shipping->shouldAllowMockingProtectedMethods();

        $quotation = m::mock(Quotation::class);
        $expected = 10.5;
        $quotation->quotation_price = $expected;

        // Expectations
        $quotation->shouldReceive('getCosts')
            ->withNoArgs()
            ->once()
            ->andReturn($expected);

        $shipping->shouldReceive('firstQuotation')
            ->withNoArgs()
            ->once()
            ->andReturn($quotation);

        // Actions
        $result = $shipping->getCosts();

        // Assertions
        $this->assertSame($expected, $result);
    }

    public function testShouldReturnTheGetDeadlineProperly()
    {
        // Set
        $shipping = m::mock(Shipping::class . '[firstQuotation]');
        $quotation = m::mock(Quotation::class);
        $expected = 4;
        $quotation->deadline = $expected;

        $shipping->shouldAllowMockingProtectedMethods();

        // Expectations
        $shipping->shouldReceive('firstQuotation')
            ->withNoArgs()
            ->once()
            ->andReturn($quotation);

        $quotation->shouldReceive('getDeadline')
            ->withNoArgs()
            ->once()
            ->andReturn($expected);

        // Actions
        $result = $shipping->getDeadline();

        // Assertions
        $this->assertSame($expected, $result);
    }

    public function testShouldGetFirstQuotation()
    {
        // Set
        $shipping = m::mock(Shipping::class . '[quotations]');
        $quotation = m::mock(Quotation::class);

        $shipping->shouldAllowMockingProtectedMethods();

        // Expectations
        $shipping->shouldReceive('quotations')
            ->withNoArgs()
            ->once()
            ->andReturn([$quotation]);

        // Actions
        $result = $shipping->firstQuotation();

        // Assertions
        $this->assertSame($quotation, $result);
    }

    public function testShouldThrowExceptionIfItHasNoQuotation()
    {
        // Set
        $shipping = m::mock(Shipping::class . '[quotations]');
        $shipping->shouldAllowMockingProtectedMethods();

        $shipping->setPostalCodeDestination('01234-000');

        // Expectations
        $shipping->shouldReceive('quotations')
            ->withNoArgs()
            ->once()
            ->andReturn([]);

        $this->expectException(QuotationNotFoundException::class);
        $this->expectExceptionMessage('No quotations were found to the given CEP: 01234-000');

        // Actions
        $this->callProtected($shipping, 'firstQuotation');
    }

    public function testShouldThrowExceptionIfDestinationIsInvalid()
    {
        // Set
        $formatter = m::mock(FormatterInterface::class);
        $shipping = m::mock(Shipping::class . '[isValid,newRequest]', [$formatter]);
        $shipping->shouldAllowMockingProtectedMethods();
        $shipping->setPostalCodeDestination('01234-000');
        $token = 't0k3n';
        $shipping::$token = $token;

        $request = m::mock(Request::class);
        $payload = '{request: "123"}';

        // Expectations
        $shipping->shouldReceive('isValid')
            ->withNoArgs()
            ->once()
            ->andReturn(true);

        $shipping->shouldReceive('newRequest')
            ->with($token)
            ->once()
            ->andReturn($request);

        $formatter->shouldReceive('setInstance')
            ->with($shipping)
            ->once();

        $formatter->shouldReceive('format')
            ->withNoArgs()
            ->once()
            ->andReturn($payload);


        $request->shouldReceive('consultShipping')
            ->with($payload)
            ->once()
            ->andThrow(new DestinationNotFoundException('Given CEP is not a valid destination: 01234-000', 101));

        $this->expectException(DestinationNotFoundException::class);
        $this->expectExceptionMessage('Given CEP is not a valid destination: 01234-000');

        // Actions
        $shipping->firstQuotation();
    }

    public function testShouldReturnQuotations()
    {
        // Set
        $shipping = m::mock(Shipping::class . '[newRequest,isValid]');
        $token = '123466';
        $request = m::mock(Request::class . '[consultShipping,quotations]', [$token]);
        $response = m::mock(Response::class);

        $shipping::$token = $token;

        $shipping->shouldAllowMockingProtectedMethods();

        // Expectations
        $request->shouldReceive('consultShipping')
            ->with('[]')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('quotations')
            ->withNoArgs()
            ->twice()
            ->andReturn([]);

        $shipping->shouldReceive('newRequest')
            ->with($token)
            ->once()
            ->andReturn($request);

        $shipping->shouldReceive('isValid')
            ->withNoArgs()
            ->twice()
            ->andReturn(true);

        // Actions
        $result1 = $shipping->quotations();
        $result2 = $shipping->quotations();

        // Assertions
        $this->assertSame([], $result1);
        $this->assertSame([], $result2);
    }

    public function testShouldReturnANewInstanceOfRequest()
    {
        // Set
        $shipping = new Shipping();

        // Actions
        $result = $this->callProtected($shipping, 'newRequest', '12345');

        // Assertions
        $this->assertTrue($result instanceof Request);
    }

    public function testShouldVerifyIfThisInstanceIsValid()
    {
        // Set
        $shipping = new Shipping();
        $volume = m::mock(VolumeInterface::class);

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setTotalPrice('21.2');
        $shipping->setAdditionalDays('12');
        $shipping->setAdditionalPrice('12.6');

        $shipping->addVolume($volume);

        // Actions
        $result = $this->callProtected($shipping, 'isValid');

        // Assertions
        $this->assertTrue($result);
    }

    public function testShouldInvalidWhenVolumeIsEmpty()
    {
        // Set
        $shipping = new Shipping();

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setTotalPrice('21.2');
        $shipping->setAdditionalDays('12');
        $shipping->setAdditionalPrice('12.6');

        // Actions
        $result = $this->callProtected($shipping, 'isValid');

        // Assertions
        $this->assertFalse($result);
    }

    public function testShouldInvalidWhenIsEmpty()
    {
        // Set
        $shipping = new Shipping();
        $volume = m::mock(VolumeInterface::class);

        $shipping->setPostalCodeOrigin('123123');
        $shipping->setPostalCodeDestination('01010');
        $shipping->setAdditionalDays('12');
        $shipping->setAdditionalPrice('12.6');

        $shipping->addVolume($volume);

        // Actions
        $result = $this->callProtected($shipping, 'isValid');

        // Assertions
        $this->assertFalse($result);
    }

    public function testShouldSetAdditionalPriceValueAsPercentage()
    {
        // Set
        $shipping = new Shipping();

        $shipping->setTotalPrice('100.10');
        $shipping->setAdditionalPrice('12%');

        $expected = [
            'valor_notafiscal' => 100.1,
            'preco_adicional' => 12.012,
        ];

        // Actions
        $result = $shipping->getAttributes();

        // Assertions
        $this->assertEquals($result, $expected);
    }

    public function testShouldRecalculateAdditionalPricePercentageWhenSetTotalPrice()
    {
        // Set
        $shipping = new Shipping();

        $shipping->setAdditionalPrice('12%');
        $shipping->setTotalPrice(85.10);

        $expected = [
            'preco_adicional' => 10.212,
            'valor_notafiscal' => 85.10,
        ];

        // Actions
        $result = $shipping->getAttributes();

        // Assertions
        $this->assertSame($result, $expected);
    }
}
