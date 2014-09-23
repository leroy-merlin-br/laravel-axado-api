<?php
namespace Axado;

use TestCase;
use Mockery as m;

class RequestTest extends TestCase
{

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testShouldDoRequestPropertly()
    {
        // Range
        $token        = "dsao231-sda0-123";
        $responseMock = m::mock('Axado\Response');
        $request      = m::mock('Axado\Request[doRequest,createResponse]', [$token]);
        $data         = "{ json: string }";
        $raw          = "{ rawResponse: true }";

        $request->shouldAllowMockingProtectedMethods();

        // Expect
        $request->shouldReceive('doRequest')
            ->with(
                "POST",
                "http://api.axado.com.br/v2/consulta/?token=$token",
                $data
            )
            ->once()
            ->andReturn($raw);

        $request->shouldReceive('createResponse')
            ->with($raw)
            ->andReturn($responseMock);

        // Act
        $response = $request->consultShipping($data);

        // Assert
        $this->assertEquals($responseMock, $response);
    }

    public function testShouldDoRequestPropertlyWhenFlagAsContracted()
    {
        // Range
        $token          = "api-token";
        $quotationToken = "cotacao-token";

        $request        = m::mock('Axado\Request[doRequest]', [$token]);
        $shipping       = m::mock('Axado\Shipping[getQuotationElected]');
        $quotation      = m::mock('Axado\Quotation[getQuotationCode]');

        $request->shouldAllowMockingProtectedMethods();

        // Expect
        $shipping->shouldReceive('getQuotationElected')
            ->once()
            ->andReturn($quotation);

        $quotation->shouldReceive('getQuotationCode')
            ->once()
            ->andReturn('100');

        $request->shouldReceive('doRequest')
            ->with(
                "PUT",
                "http://api.axado.com.br/v2/cotacao/cotacao-token/100/status/?token=api-token",
                json_encode(["status" => 2])
            )
            ->once();

        // Act
        $request->flagAsContracted($shipping, $quotationToken);
    }

    public function testShouldReturnResponseObject()
    {
        // Set
        $shipping = m::mock(new Request('2020'));

        // Act
        $result = $shipping->createResponse('raw');

        // Assert
        $this->assertTrue($result instanceof \Axado\Response);
    }
}
