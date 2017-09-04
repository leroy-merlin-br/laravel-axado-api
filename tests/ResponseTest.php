<?php
namespace Axado;

use Axado\Exception\DestinationNotFoundException;
use Axado\Exception\ShippingException;

class ResponseTest extends TestCase
{
    public function testShouldParseRawResponse()
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
        ];

        // Actions
        $response->parse($data);
        $result = $response->quotations();

        // Assertions
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Quotation::class, $result[0]);
    }

    public function testShouldNotParseIfItHasError()
    {
        // Set
        $response = new Response();
        $errorMsg = 'Destino invalido: 99999999';
        $body = ['erro_msg' => $errorMsg, 'erro_id' => 102];

        // Expectations
        $this->expectException(DestinationNotFoundException::class);
        $this->expectExceptionMessage($errorMsg);

        // Actions
        $response->parse($body);
    }

    public function testShouldNotParseIfItHasUnknownError()
    {
        // Set
        $response = new Response();
        $errorMsg = 'Deu ruim';
        $body = ['erro_msg' => $errorMsg, 'erro_id' => 999];

        // Expectations
        $this->expectException(ShippingException::class);
        $this->expectExceptionMessage($errorMsg);

        // Actions
        $response->parse($body);
    }
}
