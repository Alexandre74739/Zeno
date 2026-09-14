<?php

namespace App\Controller;

use App\Faq\FaqProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController
{
    public function __construct(
        private readonly FaqProvider $faqProvider,
    ) {
    }

    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function __invoke(): Response
    {
        return $this->render('home/index.html.twig', [
            'faqItems' => $this->faqProvider->getItems(),
        ]);
    }
}
