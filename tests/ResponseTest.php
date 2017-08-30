<?php
namespace Axado;

use Mockery as m;
use TestCase;

class ResponseTest extends TestCase
{
    public function testShouldReturnFalseWhenCallForEmptyResponseIfIsOk()
    {
        // Set
        $response = new Response();

        // Actions
        $result = $response->isOk();

        // Assertions
        $this->assertFalse($result);
    }

    public function testShouldParseTheResponseRaw()
    {
        // Set
        $response = m::mock(Response::class . '[parseQuotations,isError]');
        $response->shouldAllowMockingProtectedMethods();
        $body = ['raw' => '12.0'];

        // Expectations
        $response->shouldReceive('isError')
            ->with($body)
            ->once()
            ->andReturn(false);

        $response->shouldReceive('parseQuotations')
            ->with($body)
            ->once();

        // Actions
        $response->parse($body);
        $result = $response->isOk();

        // Assertions
        $this->assertTrue($result);
    }

    public function testShouldParseNotIfHasError()
    {
        // Set
        $response = m::mock(Response::class . '[parseQuotations,isError]');
        $response->shouldAllowMockingProtectedMethods();
        $body = ['raw' => 'body'];

        // Expectations
        $response->shouldReceive('isError')
            ->with($body)
            ->once()
            ->andReturn(true);

        $response->shouldReceive('parseQuotations')
            ->never();

        // Actions
        $response->parse($body);
        $result = $response->isOk();

        // Assertions
        $this->assertFalse($result);
    }

    public function testShouldIfNotErrorReturnFalse()
    {
        // Set
        $response = new Response();
        $data = ['right object' => true];

        // Actions
        $result = $this->callProtected($response, 'isError', [$data]);

        // Assertions
        $this->assertFalse($result);
    }

    public function testShouldReturnTrueIfHasError()
    {
        // Set
        $response = new Response();
        $data = ['erro_id' => '123', 'erro_msg' => 'cep não encontrado'];

        // Actions
        $result = $this->callProtected($response, 'isError', [$data]);

        // Assertions
        $this->assertTrue($result);
    }

    public function testShouldReturnTrueIfHasEmptyArrayError()
    {
        // Set
        $response = new Response();
        $data = [];

        // Expectations
        $result = $this->callProtected($response, 'isError', [$data]);

        // Assertions
        $this->assertTrue($result);
    }

    public function testShouldParseQuotations()
    {
        // Set
        $response = new Response();
        $data = [
            'cotacoes' => [
                [
                    'transportadora_metaname' => 'correios',
                    'servico_metaname' => 'correios-pac',
                ],
            ],
            'consulta_token' => 'token consulta',
        ];

        // Actions
        $this->callProtected($response, 'parseQuotations', [$data]);
        $result = $response->quotations();

        // Assertions
        $this->assertTrue(is_array($result));
        $this->assertSame('token consulta', $response->getQuotationToken());
    }
}
