<?php

namespace App\Repository;

use App\Entity\SurveyResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SurveyResult>
 *
 * @method SurveyResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method SurveyResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method SurveyResult[]    findAll()
 * @method SurveyResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SurveyResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyResult::class);
    }

    public function save(SurveyResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SurveyResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getReport(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                sr.id,
                srq.question_id,
                q.text AS question_text,
                sra.answer_id,
                a.text as answer_text,
                count(sra.answer_id) AS cnt            
            FROM survey_result AS sr
            LEFT JOIN survey_result_question as srq on srq.survey_result_id = sr.id
            LEFT JOIN question as q on srq.question_id = q.id
            LEFT JOIN survey_result_answer as sra on sra.survey_result_id = sr.id
            LEFT JOIN answer as a on sra.answer_id = a.id
            WHERE a.question_id = srq.question_id
            GROUP by sra.answer_id 
            ORDER by srq.question_id ASC
        ';
        $stmt = $conn->prepare($sql);
        $rows = $stmt->executeQuery()->fetchAllAssociative();

        $result = [];

        foreach ($rows as $row) {
            if (isset($result[$row['question_id']]) == false) {
                $result[$row['question_id']] = ['Text' => $row['question_text']];
                $result[$row['question_id']]['Total'] = 0;
                $result[$row['question_id']]['Answers'] = [];
                
            }

            $result[$row['question_id']]['Answers'][] = [$row['answer_text'] => $row['cnt']];
            $result[$row['question_id']]['Total'] += $row['cnt'];
        }

        // $result = [];

        return $result;

    }

//    /**
//     * @return SurveyResult[] Returns an array of SurveyResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SurveyResult
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}