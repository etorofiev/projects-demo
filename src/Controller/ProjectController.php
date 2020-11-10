<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     * @param Request $request
     * @param ClientInterface $localApiClient
     * @return Response
     */
    public function index(
        Request $request,
        ClientInterface $localApiClient
    ): Response
    {
        $page = $request->query->get('page', 1);

        try {
            $response = $localApiClient->request('GET', 'projects?page=' . $page);
            $body = (string) $response->getBody();
            $projectsData = json_decode($body, true);
        } catch (GuzzleException $e) {
            ////TODO handle the different guzzle exceptions in different ways
            return $this->render('project/index.html.twig', [
                'projects' => [],
                'clientErrors' => [$e->getMessage()]
            ]);
        }

        if ($projectsData['code'] === 0) {
            return $this->render('project/index.html.twig', [
                'projects' => $projectsData['data'][0],
            ]);
        } else {
            return $this->render('project/index.html.twig', [
                'projects' => [],
                'clientErrors' => $projectsData['errors']
            ]);
        }
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     * @param Request $request
     * @param ClientInterface $localApiClient
     * @return Response
     */
    public function new(Request $request, ClientInterface $localApiClient): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            unset($data['project']['_token']);

            try {
                $response = $localApiClient->request('POST', 'projects/', ['json' => $data]);
                $body = (string) $response->getBody();
                $projectsData = json_decode($body, true);

                if ($projectsData['code'] === 0) {
                    $this->addFlash('success', 'Project has been added successfully');
                } else {
                    $this->addFlash('error', implode(', ', $projectsData['errors']));
                }

            } catch (GuzzleException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @param ClientInterface $localApiClient
     * @return Response
     */
    public function show(int $id, ClientInterface $localApiClient): Response
    {
        try {
            $response = $localApiClient->request('GET', 'projects/' . $id);
            $body = (string) $response->getBody();
            $projectsData = json_decode($body, true);
        } catch (GuzzleException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('project_index');
        }

        if ($projectsData['code'] !== 0) {
            $this->addFlash('error', implode(', ', $projectsData['errors']));
            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/show.html.twig', [
            'project' => $projectsData['data']['project'][0],
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Project $project
     * @param ClientInterface $localApiClient
     * @return Response
     */
    public function edit(Request $request, Project $project, ClientInterface $localApiClient): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            unset($data['project']['_token']);

            try {
                $response = $localApiClient->request('PUT', 'projects/' . $project->getId(), ['json' => $data]);
                $body = (string) $response->getBody();
                $projectsData = json_decode($body, true);

                if ($projectsData['code'] === 0) {
                    $this->addFlash('success', 'Project has been updated successfully');
                } else {
                    $this->addFlash('error', implode(', ', $projectsData['errors']));
                }

            } catch (GuzzleException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param int $id
     * @param ClientInterface $localApiClient
     * @return Response
     */
    public function delete(Request $request, int $id, ClientInterface $localApiClient): Response
    {
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            try {
                $response = $localApiClient->request('DELETE', 'projects/' . $id);
                $body = (string) $response->getBody();
                $projectsData = json_decode($body, true);

                if ($projectsData['code'] === 0) {
                    $this->addFlash('success', 'Project has been deleted successfully');
                } else {
                    $this->addFlash('error', implode(', ', $projectsData['errors']));
                }

            } catch (GuzzleException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('project_index');
        }

        return $this->redirectToRoute('project_index');
    }
}
