<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $response = new Response();

        // no twig so let's do it savage style :)
        // no need to load twig for this one page
        $response->setContent('<ul>
        <li><a href="/api/user">/api/user</a></li>
        </ul>');

        return $response;
    }
}
