<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/poll')]
class PollController extends AbstractController
{
    public function __construct(private ChartBuilderInterface $chartBuilder, private AnswerRepository $answerRepository) {}

    #[Route('/{id}', name: 'poll_show', methods: ['GET'])]
    public function show(Request $request, Poll $poll, ): Response
    {
        return $this->render('poll/show.html.twig', [
            'poll' => $poll,
        ]);
    }

    public function chart(Poll $poll)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart
            ->setData([
                'labels' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
                'datasets' => [
                    [
                        'label' => 'Nombre de présents',
                        'backgroundColor' => '#fc3675',
                        'borderColor' => '#fc3675',
                        'data' => $this->answerRepository->countsForPoll($poll),
                    ],
                ],
            ])
            ->setOptions([
                'scales' => [
                    'yAxes' => [['ticks' => ['stepSize' => 1, 'min' => 0]]],
                ],
            ])
        ;

        return $this->render('poll/chart.html.twig', [
            'chart' => $chart,
        ]);
    }
}
