<?php
namespace Axado;

class QuotationTest extends TestCase
{
    public function testShouldFillCorrectly()
    {
        // Set
        $quotation = new Quotation();
        $quotation->fill(
            [
                'transportadora_metaname' => 'Correiros',
                'servico_metaname' => 'Correios-pac',
                'servico_nome' => 'pac',
                'cotacao_preco' => '10.5',
                'cotacao_custo' => '10.3',
                'cotacao_prazo' => '4',
                'non field' => 'non value',
            ]
        );

        $expected = [
            'name' => 'Correiros',
            'service_metaname' => 'Correios-pac',
            'service_name' => 'pac',
            'quotation_price' => '10.5',
            'quotation_costs' => '10.3',
            'deadline' => '4',
        ];

        // Actions
        $result = $quotation->attributes();

        // Assertions
        $this->assertSame($expected, $result);
    }

    public function testShouldReturnNullCost()
    {
        // Set
        $quotation = new Quotation();

        // Actions
        $result = $quotation->getCosts();

        // Assertions
        $this->assertNull($result);
    }

    public function testShouldReturnTheCosts()
    {
        // Set
        $quotation = new Quotation();
        $quotation->fill(
            [
                'cotacao_preco' => '123,1',
            ]
        );

        // Actions
        $result = $quotation->getCosts();

        // Assertions
        $this->assertSame('123,1', $result);
    }

    public function testShouldReturnTheDeadline()
    {
        // Set
        $quotation = new Quotation();

        $this->assertNull($quotation->getDeadline());

        $data = [
            'cotacao_prazo' => '12',
        ];

        // Actions
        $quotation->fill($data);

        // Assertions
        $this->assertSame('12', $quotation->getDeadline());
    }

    public function testShouldGetAttributes()
    {
        // Set
        $quotation = new Quotation();
        $quotation->fill(
            [
                'transportadora_metaname' => 'FooBar',
            ]
        );

        // Actions
        $result = $quotation->name;

        // Assertions
        $this->assertSame('FooBar', $result);
    }
}
