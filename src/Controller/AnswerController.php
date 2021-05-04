<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Poll;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use App\Repository\PollRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/answer')]
class AnswerController extends AbstractController
{
    public function __construct(private HubInterface $hub, private AnswerRepository $answerRepository, private PollRepository $pollRepository) {}

    #[Route('/', name: 'answer_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $poll = $this->pollRepository->latest();
        $answer = $this->answerRepository->findOneBy(['poll' => $poll, 'respondent' => $this->getUser()]) ?? new Answer();

        return $this->handleForm(
            $this->createForm(AnswerType::class, $answer),
            $request,
            function (FormInterface $form) use ($answer, $poll) {
                $answer->setPoll($poll);
                $answer->setRespondent($this->getUser());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($answer);
                $entityManager->flush();

                $this->hub->publish(new Update(
                   'poll'.$poll->getId(),
                   $this->renderView('poll/show.stream.html.twig', ['poll' => $poll])
                ));

                return $this->redirectToRoute('answer_new', [], Response::HTTP_SEE_OTHER);
            },
            function (FormInterface $form) use ($answer) {
                return $this->render('answer/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        );
    }
}
