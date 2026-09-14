<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    #[Route('/{_locale}/mentions-legales', name: 'app_legal_notice', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function legalNotice(): Response
    {
        return $this->render('legal/legal_notice.html.twig');
    }

    #[Route('/{_locale}/politique-de-confidentialite', name: 'app_privacy_policy', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function privacyPolicy(): Response
    {
        return $this->render('legal/privacy_policy.html.twig');
    }

    #[Route('/{_locale}/politique-de-cookies', name: 'app_cookie_policy', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function cookiePolicy(): Response
    {
        return $this->render('legal/cookie_policy.html.twig');
    }
}
