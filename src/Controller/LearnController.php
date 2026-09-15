<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\User;
use App\Form\AnswerType;
use App\Quiz\QuizClock;
use App\Quiz\QuizProgressService;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LearnController extends AbstractController
{
    #[Route('/{_locale}/apprendre', name: 'app_learn', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    #[IsGranted('ROLE_USER')]
    public function index(QuizProgressService $progressService, QuestionRepository $questionRepository, QuizClock $clock): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $today = $clock->today();

        $progress = $progressService->getOrCreateProgress($user);
        $progressService->catchUpMissedDays($progress, $today);

        $question = $questionRepository->findForDate($today);
        $todaysAnswer = $question ? $progressService->findAnswerForDate($user, $today) : null;

        $form = null;
        if ($question && !$todaysAnswer) {
            $form = $this->createForm(AnswerType::class, null, ['question' => $question]);
        }

        return $this->render('learn/index.html.twig', [
            'progress' => $progress,
            'question' => $question,
            'todaysAnswer' => $todaysAnswer,
            'form' => $form?->createView(),
        ]);
    }

    #[Route('/{_locale}/apprendre/questions/{id}/repondre', name: 'app_learn_answer', requirements: ['_locale' => 'fr|en'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function answer(Question $question, Request $request, QuizProgressService $progressService, QuizClock $clock): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $today = $clock->today();

        $form = $this->createForm(AnswerType::class, null, ['question' => $question]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choice = $form->get('choice')->getData();

            try {
                $answer = $progressService->recordAnswer($user, $question, $choice, $today);
                if (!$answer) {
                    $this->addFlash('wrong_choice_id', (string) $choice->getId());
                }
            } catch (\DomainException) {
                // Already answered correctly today: the index page reflects that on its own.
            }
        }

        return $this->redirectToRoute('app_learn', ['_locale' => $request->getLocale()]);
    }
}
