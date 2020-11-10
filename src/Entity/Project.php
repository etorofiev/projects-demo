<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
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
     *     minMessage="The project title should be at least 3 characters",
     *     maxMessage="The project title should be at most 255 characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4095, nullable=true)
     * @Assert\Length(max=4095, allowEmptyString=true,
     *     maxMessage="The description should be at most 4095 characters"
     * )
     */
    private $description;

    /**
     * Warning! DO NOT add ChoiceType to the form class, otherwise validating the status will result
     * in a generic error message "This value is not valid"
     *
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
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="projects")
     * @MaxDepth(1)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="projects")
     * @MaxDepth(1)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="project")
     * @MaxDepth(1)
     */
    private $tasks;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function __toString()
    {
        if (!empty($this->title)) {
            return $this->getTitle();
        } else {
            return 'Unknown project';
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

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
