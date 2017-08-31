<?php
namespace Axado;

use Mockery as m;


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
