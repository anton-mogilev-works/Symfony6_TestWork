<?php

namespace App\Controller;

use App\Entity\SurveyResult;
use App\Repository\SurveyResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Answer;
use App\Entity\Question;

class SurveyController extends AbstractController
{
    // Стартовый метод
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        return $this->render('survey/index.html.twig', [

        ]);
    }

    /*
     * Метод обработки и выдачи данных
     * Согласно ТЗ обмен данными должен происходить без обновления страницы, поэтому формирование формы с вопросом
     * и вывод статистики проще разместить в одном методе    
     */

    #[Route('/io_data', name: 'data')]
    public function ioData(EntityManagerInterface $entityManager, Request $request, LoggerInterface $logger, MailerInterface $mailer): Response
    {
        $session = $request->getSession();

        $question_id = $request->request->get('question_id');
        $answer_id = $request->request->all('answer_id');

        $ipAddress = $request->getClientIp();

        if (is_null($session->get('survey_result_id'))) {
            $surveyResult = new SurveyResult();
            $surveyResult->setIpAddress($ipAddress);
            $entityManager->persist($surveyResult);
            $entityManager->flush();
            $session->set('survey_result_id', $surveyResult->getId());

        } else {
            $surveyResult = $entityManager->getRepository(SurveyResult::class)->findOneBy(['id' => intval($session->get('survey_result_id'))]);

            if (is_null($surveyResult)) {
                $surveyResult = new SurveyResult();
                $surveyResult->setIpAddress($ipAddress);
                $entityManager->persist($surveyResult);
                $entityManager->flush();
                $session->set('survey_result_id', $surveyResult->getId());
            }
        }

        // Если не пришло данных по форме
        if (is_null($question_id) || is_null($answer_id)) {
            // Берем вопрос с ID = 1
            $question = $entityManager->getRepository(Question::class)->findOneBy(['id' => 1]);

            $type = $question->getType();

            // И сразу отправляем в ответе на запрос
            return $this->render('survey/data.html.twig', [
                'question' => $question,
                'type' => $type

            ]);
        }
        // Если данные по форме пришли
        else {

            // Получаем IP адрес запроса

            $questionResult = $entityManager->getRepository(Question::class)->findOneBy(['id' => $question_id]);
            $surveyResult->addQuestion($questionResult);


            // Если множетсвенный овтет
            if (is_array($answer_id)) {
                foreach ($answer_id as $a_id) {
                    $answerResult = $entityManager->getRepository(Answer::class)->findOneBy(['id' => $a_id]);
                    $surveyResult->addAnswer($answerResult);

                    // Если у ответа есть ссылка на следующий вопрос (подразумеваем что только у одного ответа в массиве)
                    if (is_null($answerResult) === false && $answerResult instanceof Answer) {
                        $question = $answerResult->getNextQuestion();
                    }

                }
            }
            // Записываем в таблицу результатов
            $entityManager->persist($surveyResult);
            $entityManager->flush();

        }

        // Если получен не пустой вопрос
        if (isset($question) && is_null($question) == false) {
            $type = $question->getType();

            $view = $this->renderView('survey/data.html.twig', [
                'question' => $question,
                'type' => $type

            ]);
        }
        // Если вопросов больше нет - выводим статистику
        else {
            $repository = $entityManager->getRepository(SurveyResult::class);
            $report = $repository->getReport();
            $session->clear();
            $view = $this->renderView('survey/statistics.html.twig', [
                'report' => $report
            ]);

            $email = (new Email())
                ->from('4530@spk.ru')
                ->to('4530@spk.ru')
                ->subject('Отчёт о прохождении опроса')
                ->html($view);

            $mailer->send($email);   
        }

        return new Response($view);
    }


    #[Route('/test', name: 'test')]
    public function test(EntityManagerInterface $entityManager, Request $request, LoggerInterface $logger, MailerInterface $mailer): Response
    {

        $repository = $entityManager->getRepository(SurveyResult::class);
        $report = $repository->getReport();        

        return (new JsonResponse($report))->setEncodingOptions(JSON_UNESCAPED_UNICODE)->setPublic();

    }



}