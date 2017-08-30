<?php
namespace Axado;

use TestCase;

class QuotationTest extends TestCase
{
    public function testShouldFillCorrectly()
    {
        // Set
        $quotation = new Quotation;

        $data = [
            "transportadora_metaname" => "Correiros",
            "servico_metaname"        => "Correios-pac",
            "servico_nome"            => "pac",
            "cotacao_preco"           => "10.5",
            "cotacao_custo"           => "10.3",
            "cotacao_prazo"           => "4",
            "non field"               => 'non value'
        ];

        // Act
        $quotation->fill($data);
        $result = $quotation->attributes();

        // Assert
        $expected = [
            'name'             => 'Correiros',
            'service_metaname' => 'Correios-pac',
            'service_name'     => 'pac',
            'quotation_price'  => '10.5',
            'quotation_costs'  => '10.3',
            'deadline'         => '4',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testShouldReturnNullWithNoTheQuotationToken()
    {
        // Set
        $quotation = new Quotation;

        // Assert
        $this->assertNull($quotation->getQuotationCode());
    }

    public function testShouldReturnTheQuotationToken()
    {
        // Set
        $quotation = new Quotation;

        $data = [
            "cotacao_codigo" => "123"
        ];

        // Act
        $quotation->fill($data);

        // Assert
        $this->assertEquals("123", $quotation->getQuotationCode());
    }

    public function testShouldReturnTheCosts()
    {
        // Set
        $quotation = new Quotation;

        $this->assertNull($quotation->getCosts());

        $data = [
            "cotacao_preco" => "123,1"
        ];

        // Act
        $quotation->fill($data);

        // Act
        $this->assertEquals("123,1", $quotation->getCosts());
    }

    public function testShouldReturnTheDeadline()
    {
        // Set
        $quotation = new Quotation;

        $this->assertNull($quotation->getDeadline());

        $data = [
            "cotacao_prazo" => "12"
        ];

        // Act
        $quotation->fill($data);

        // Assert
        $this->assertEquals("12", $quotation->getDeadline());
    }

    public function testShouldGetAttributes()
    {
        // Set
        $quotation = new Quotation;

        $data = [
            "transportadora_metaname" => "FooBar"
        ];
        // Act
        $quotation->fill($data);

        // Assert
        $this->assertEquals(
            'FooBar',
            $quotation->name
        );
    }
}
