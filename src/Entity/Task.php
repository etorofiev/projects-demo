<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255,
     *     minMessage="The task title should be at least 3 characters",
     *     maxMessage="The task title should be at most 255 characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4095, nullable=true)
     * @Assert\Length(max=4095, allowEmptyString=true,
     *     maxMessage="The task description should be at most 4095 characters"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please provide a valid status - new, pending, failed or done")
     * @Assert\Choice(choices={"new", "pending", "failed", "done"}, message="Invalid status - the available options are: new, pending, failed or done")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please provide a duration in minutes")
     * @Assert\GreaterThanOrEqual(0, message="Please provide a duration in minutes between 0 and 8388607")
     * @Assert\LessThan(8388607, message="The duration cannot exceed 8388607")
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="tasks")
     * @MaxDepth(1)
     * @Assert\NotBlank(message="Please select a project for the task")
     */
    private $project;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;

    public function __toString()
    {
        if (!empty($this->title)) {
            return $this->getTitle();
        } else {
            return 'Unknown task';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
