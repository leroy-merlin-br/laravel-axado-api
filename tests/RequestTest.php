<?php
namespace Axado;

use Mockery as m;
use TestCase;

class RequestTest extends TestCase
{
    public function testShouldDoRequestProperly()
    {
        // Set
        $token = 'dsao231-sda0-123';
        $response = m::mock(Response::class);
        $request = m::mock(
            Request::class . '[doRequest,createResponse]',
            [$token]
        );
        $request->shouldAllowMockingProtectedMethods();
        $data = '{ json: string }';
        $raw = ['rawResponse' => true];

        // Expectations
        $request->shouldReceive('doRequest')
            ->with(
                'POST',
                "http://api.axado.com.br/v2/consulta/?token={$token}",
                $data
            )
            ->once()
            ->andReturn($raw);

        $request->shouldReceive('createResponse')
            ->with($raw)
            ->once()
            ->andReturn($response);

        // Actions
        $result = $request->consultShipping($data);

        // Assertions
        $this->assertSame($response, $result);
    }

    public function testShouldDoRequestProperlyWhenFlagAsContracted()
    {
        // Set
        $token = 'api-token';
        $quotationToken = 'cotacao-token';

        $request = m::mock(Request::class . '[doRequest]', [$token]);
        $request->shouldAllowMockingProtectedMethods();

        $shipping = m::mock(Shipping::class . '[getElectedQuotation]');
        $quotation = m::mock(Quotation::class . '[getQuotationCode]');

        // Expectations
        $shipping->shouldReceive('getElectedQuotation')
            ->withNoArgs()
            ->once()
            ->andReturn($quotation);

        $quotation->shouldReceive('getQuotationCode')
            ->withNoArgs()
            ->once()
            ->andReturn('100');

        $request->shouldReceive('doRequest')
            ->with(
                'PUT',
                'http://api.axado.com.br/v2/cotacao/cotacao-token/100/status/?token=api-token',
                json_encode(['status' => 2])
            )
            ->once();

        // Actions
        $request->flagAsContracted($shipping, $quotationToken);
    }

    public function testShouldReturnResponseObject()
    {
        // Set
        $shipping = m::mock(new Request('2020'));

        // Actions
        $result = $shipping->createResponse('raw');

        // Assertions
        $this->assertInstanceOf(Response::class, $result);
    }
}
