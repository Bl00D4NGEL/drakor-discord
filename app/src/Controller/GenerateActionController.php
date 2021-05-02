<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GenerateActionController
{
    /**
     * @Route("/create/action")
     */
    public function __invoke(): Response
    {
        // Load data from post request
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
