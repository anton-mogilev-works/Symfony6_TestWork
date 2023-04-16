<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class)]
    private Collection $answers;

    #[ORM\OneToMany(mappedBy: 'next_question', targetEntity: Answer::class)]
    private Collection $binded_answers;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->binded_answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getBindedAnswers(): Collection
    {
        return $this->binded_answers;
    }

    public function addBindedAnswer(Answer $bindedAnswer): self
    {
        if (!$this->binded_answers->contains($bindedAnswer)) {
            $this->binded_answers->add($bindedAnswer);
            $bindedAnswer->setNextQuestion($this);
        }

        return $this;
    }

    public function removeBindedAnswer(Answer $bindedAnswer): self
    {
        if ($this->binded_answers->removeElement($bindedAnswer)) {
            // set the owning side to null (unless already changed)
            if ($bindedAnswer->getNextQuestion() === $this) {
                $bindedAnswer->setNextQuestion(null);
            }
        }

        return $this;
    }

    public function toArray() : array
    {
        return ['hello' => 'there'];
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
