<?php
namespace Axado;

use TestCase;
use Mockery as m;

class QuotationTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldFillCorrectly()
    {
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

        $quotation->fill($data);

        $result = $quotation->attributes();
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
}
