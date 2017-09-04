<?php
namespace Axado;

use Mockery as m;

class RequestTest extends TestCase
{
    public function testShouldDoRequestProperly()
    {
        // Set
        $token = 'dsao231';
        $request = m::mock(Request::class.'[doRequest]', [$token]);
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

        // Actions
        $result = $request->consultShipping($data);

        // Assertions
        $this->assertInstanceOf(Response::class, $result);
    }
}
