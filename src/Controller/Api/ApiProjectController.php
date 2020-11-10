<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/projects")
 */
class ApiProjectController extends AbstractController
{
    use SerializesEntitiesToJson;

    const PAGE_RESULTS = 20;

    /**
     * @Route("/", name="api_project_list", format="json", methods={"GET"})
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @return JsonResponse
     */
    public function index(Request $request, ProjectRepository $projectRepository): JsonResponse
    {
        $page = $request->query->get('page', 1);

        try {
            $projects = $projectRepository->findAllPaginated($page);
            $extraData = [
                'total' => $projects->count(),
                'limit' => self::PAGE_RESULTS,
                'maxPages' => ceil($projects->count() / self::PAGE_RESULTS),
                'page' => (int) $page
            ];
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => -1,
                'errors' => [$e->getMessage()]
            ]);
        }

        return $this->serializeEntityToJsonResponse($projects, 0, $extraData);
    }

    /**
     * @Route("/{id}", name="api_project_show", format="json", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @param ProjectRepository $projectRepository
     * @return JsonResponse
     */
    public function find(int $id, ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->findBy(['id' => $id, 'deleted' => false]);

        if (empty($project)) {
            return new JsonResponse([
                'code' => -1,
                'errors' => ['Project not found']
            ]);
        } else {
            return $this->serializeEntityToJsonResponse($project, 'project');
        }
    }

    /**
     * @Route("/", name="api_project_create", format="json", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->serializeEntityToJsonResponse($project, 'project');
        } else {
            return new JsonResponse([
                'code' => -1,
                'errors' => explode("\n", trim((string) $form->getErrors(true)))
            ]);
        }
    }

    /**
     * @Route("/{id}", name="api_project_update", format="json", methods={"PUT"}, requirements={"id"="\d+"})
     * @param int $id
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @return JsonResponse
     */
    public function update(int $id, Request $request, ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->findOneBy(['id' => $id, 'deleted' => false]);

        if (empty($project)) {
            return new JsonResponse(
                [
                    'code' => -1,
                    'errors' => ['Project not found']
                ]
            );
        }

        $form = $this->createForm(ProjectType::class, $project, ['csrf_protection' => false, 'method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->serializeEntityToJsonResponse($project, 'project');
        } else {
            return new JsonResponse([
                'code' => -1,
                'errors' => explode("\n", trim((string) $form->getErrors(true)))
            ]);
        }
    }

    /**
     * @Route("/{id}", name="api_project_delete", format="json", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param int $id
     * @param ProjectRepository $projectRepository
     * @return JsonResponse
     */
    public function delete(int $id, ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->findOneBy(['id' => $id, 'deleted' => false]);

        if (empty($project)) {
            return new JsonResponse([
                'code' => -1,
                'errors' => ['Project not found']
            ]);
        } else {
            $project->setDeleted(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return new JsonResponse([
                'code' => 0,
                'message' => 'Project has been deleted successfully'
            ]);
        }
    }
}
