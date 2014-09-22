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
            ->with("POST", "http://api.axado.com.br/v2/consulta/?token=$token", $data)
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

    public function testShouldReturnResponseObject()
    {
        $shipping = m::mock(new Request('2020'));

        $result = $shipping->createResponse('raw');

        $this->assertTrue($result instanceof \Axado\Response);
    }
}
