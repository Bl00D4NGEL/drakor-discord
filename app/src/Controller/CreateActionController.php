<?php

declare(strict_types=1);


namespace App\Controller;

use App\ValueObject\LocationProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreateActionController extends AbstractController
{
    private LocationProvider $locationProvider;

    public function __construct(LocationProvider $locationProvider)
    {
        $this->locationProvider = $locationProvider;
    }

    /**
     * @Route("/create/action", methods={"GET"})
     */
    public function __invoke(): Response
    {
        return $this->render('create-action.html.twig', [
            'locations' => $this->locationProvider->getGuildNodes()
        ]);
    }

}
