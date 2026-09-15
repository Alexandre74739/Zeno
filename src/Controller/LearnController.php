<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LearnController extends AbstractController
{
    #[Route('/{_locale}/apprendre', name: 'app_learn', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(): Response
    {
        return $this->render('learn/index.html.twig');
    }
}
