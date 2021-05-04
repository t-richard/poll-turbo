<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Poll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function countsForPoll(Poll $poll)
    {
        return array_values($this->createQueryBuilder('answer')
            ->select('COUNT_FILTER(answer.monday, where answer.monday = true), COUNT_FILTER(answer.tuesday, where answer.tuesday = true), COUNT_FILTER(answer.wednesday, where answer.wednesday = true), COUNT_FILTER(answer.thursday, where answer.thursday = true), COUNT_FILTER(answer.friday, where answer.friday = true)')
            ->where('answer.poll = :poll')
            ->setParameter('poll', $poll)
            ->getQuery()
            ->getSingleResult())
        ;
    }
}
