<?php

namespace App\Seeds;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Seed
{

    public static function seedName(): string
    {
        return 'start';
    }

    public static function getOrder(): int
    {
        return 0;
    }

    /**
     * The load method is called when loading a seed 
     */
    public function load(InputInterface $input, OutputInterface $output): int
    {
        $manager = $this->getManager();
        $this->disableDoctrineLogging();


        $question = (new Question())->setText("Вы пользуетесь социальными сетями?")->setType("radio");
        $question_2 = (new Question())->setText("В какой социальной сети вы зарегистрированы?")->setType("checkbox");

        $answer_yes = (new Answer())->setText("Да");
        $answer_no = (new Answer())->setText("Нет");
        $answer_yes->setNextQuestion($question_2);

        $answer_vk = (new Answer())->setText("Вконтакте");
        $answer_ok = (new Answer())->setText("Одноклассники");
        $answer_mk = (new Answer())->setText("Мой круг");

        $question_2->addAnswer($answer_vk)->addAnswer($answer_ok)->addAnswer($answer_mk);
        $question->addAnswer($answer_yes)->addAnswer($answer_no);        

        $manager->persist($question);
        $manager->persist($question_2);
        $manager->persist($answer_yes);
        $manager->persist($answer_no);
        $manager->persist($answer_vk);
        $manager->persist($answer_ok);
        $manager->persist($answer_mk);

        $manager->flush();

        return 0;
    }
    
    public function unload(InputInterface $input, OutputInterface $output): int
    {        
        $this->manager->getConnection()->exec('DELETE FROM question');
        $this->manager->getConnection()->exec('DELETE FROM answer');
       
        return 0;
    }

}