<?php
namespace Axado;

use TestCase;
use Mockery as m;

class RequestTest extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldDoRequestPropertly()
    {
        $token    = "dsao231-sda0-123";
        $request  = new Request($token);
        $shipping = m::mock('Axado\Shipping');

        $response = $request->consultShipping("{ json: string }");
    }
}
