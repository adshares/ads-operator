<?php

namespace Adshares\AdsOperator\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index()
    {
        return new Response(
            '<html><body>Ads Operator</body></html>'
        );
    }
}
