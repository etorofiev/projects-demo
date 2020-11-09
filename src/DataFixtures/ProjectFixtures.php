<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Repository\ClientRepository;
use App\Repository\CompanyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ProjectFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    public function __construct(CompanyRepository $companyRepository, ClientRepository $clientRepository)
    {
        $this->faker = Factory::create();
        $this->companyRepository = $companyRepository;
        $this->clientRepository = $clientRepository;
    }

    public function load(ObjectManager $manager)
    {
        $companies = $this->companyRepository->findAll();
        $clients = $this->clientRepository->findAll();

        foreach ($companies as $company) {
            for ($i = 0; $i < 3; $i++) {
                $project = $this->createGenericProject();
                $project->setCompany($company);
                $manager->persist($project);
            }
        }

        foreach ($clients as $client) {
            for ($i = 0; $i < 2; $i++) {
                $project = $this->createGenericProject();
                $project->setClient($client);
                $manager->persist($project);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class,
            ClientFixtures::class
        ];
    }

    /**
     * @return Project
     */
    protected function createGenericProject(): Project
    {
        $statuses = ['new', 'pending', 'failed', 'done'];

        $project = new Project();
        $project->setTitle($this->faker->colorName . ' ' . $this->faker->jobTitle);
        $project->setDescription($this->faker->sentence);
        $project->setStatus($statuses[array_rand($statuses)]);
        $project->setDuration(60);

        return $project;
    }
}
