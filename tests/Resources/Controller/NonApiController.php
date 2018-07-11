<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/non-api")
 */
class NonApiController extends Controller
{
    /**
     * @Route("/test")
     */
    public function testAction()
    {
        $response = new Response();
        $response->setContent('Test');

        return $response;
    }

    /**
     * @Route("/exception")
     */
    public function exceptionAction()
    {
        throw new Exception('Test');
    }
}
