<?php
namespace Axado;

use TestCase;
use Mockery as m;

class ResponseTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldReturnFalseWhenCallForEmptyResponseIfIsOk()
    {
        // Set
        $response = new Response;

        // Assert
        $this->assertFalse($response->isOk());
    }

    public function testShouldParseTheResponseRaw()
    {
        // Set
        $response = m::mock('Axado\Response[parseQuotations,isError]');
        $response->shouldAllowMockingProtectedMethods();

        // Expect
        $response->shouldReceive('isError')
            ->once()
            ->andReturn(false);

        $response->shouldReceive('parseQuotations')
            ->once()
            ->with(['raw' => '12.0']);

        // Assert
        $response->parse(['raw' => '12.0']);
        $this->assertTrue($response->isOk());
    }

    public function testShouldParseNotIfHasError()
    {
        // Set
        $response = m::mock('Axado\Response[parseQuotations,isError]');
        $response->shouldAllowMockingProtectedMethods();

        // Expect
        $response->shouldReceive('isError')
            ->once()
            ->andReturn(true);

        $response->shouldReceive('parseQuotations')
            ->never();

        // Assert
        $response->parse("{ raw }");
        $this->assertFalse($response->isOk());
    }

    public function testShouldIfNotErrorReturnFalse()
    {
        // Set
        $response = new Response;
        $data = ["right object" => true];

        // Expect
        $result = $this->callProtected($response, 'isError', [$data]);

        // Assert
        $this->assertFalse($result);
    }

    public function testShouldReturnTrueIfHasError()
    {
        // Set
        $response = new Response;
        $data = ["erro_id" => '123', "erro_msg" => 'cep não encontrado'];

        // Expect
        $result =  $this->callProtected($response, 'isError', [$data]);

        // Assert
        $this->assertTrue($result);
    }

    public function testShouldReturnTrueIfHasEmptyArrayError()
    {
        // Set
        $response = new Response;
        $data = [];

        // Expect
        $result =  $this->callProtected($response, 'isError', [$data]);

        // Assert
        $this->assertTrue($result);
    }

    public function testShouldParseQuotations()
    {
        // Set
        $response = new Response;
        $data = [
            "cotacoes" => [
                [
                    "transportadora_metaname" => "correios",
                    "servico_metaname"        => "correios-pac"
                ]
            ],
            "consulta_token" => "token consulta"
        ];

        // Act
        $this->callProtected($response, 'parseQuotations', [$data]);
        $result = $response->quotations();

        // Assert
        $this->assertTrue(is_array($result));

        $this->assertEquals("token consulta", $response->getQuotationToken());
    }
}
