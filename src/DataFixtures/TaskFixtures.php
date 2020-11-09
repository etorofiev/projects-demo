<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class TaskFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->faker = Factory::create();
        $this->projectRepository = $projectRepository;
    }

    public function load(ObjectManager $manager)
    {
        $statuses = ['new', 'pending', 'failed', 'done'];
        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            for ($i = 0; $i < 5; $i++) {
                $task = new Task();
                $task->setProject($project);
                $task->setTitle($this->faker->colorName . ' ' . $this->faker->jobTitle);
                $task->setDescription($this->faker->sentence);
                $task->setStatus($statuses[array_rand($statuses)]);
                $task->setDuration(12);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixtures::class,
        ];
    }
}
