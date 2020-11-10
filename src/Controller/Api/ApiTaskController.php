<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/tasks")
 */
class ApiTaskController extends AbstractController
{
    use SerializesEntitiesToJson;

    /**
     * @Route("/", name="api_task_list", format="json", methods={"GET"})
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function index(Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $page = $request->query->get('page', 1);

        try {
            $tasks = $taskRepository->findAllPaginated($page);
            $extraData = [
                'total' => $tasks->count(),
                'limit' => $tasks->getIterator()->count(),
                'maxPages' => ceil($tasks->count() / $tasks->getIterator()->count()),
                'page' => (int) $page
            ];
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => -1,
                'errors' => [$e->getMessage()]
            ]);
        }

        return $this->serializeEntityToJsonResponse($tasks, 0 , $extraData);
    }

    /**
     * @Route("/{id}", name="api_task_show", format="json", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function find(int $id, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findBy(['id' => $id, 'deleted' => false]);

        if (empty($task)) {
            return new JsonResponse([
                'code' => -1,
                'errors' => ['Task not found']
            ]);
        } else {
            return $this->serializeEntityToJsonResponse($task, 'task');
        }
    }

    /**
     * @Route("/", name="api_task_create", format="json", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->serializeEntityToJsonResponse($task, 'task');
        } else {
            return new JsonResponse([
                'code' => -1,
                'errors' => explode("\n", trim((string) $form->getErrors(true)))
            ]);
        }
    }

    /**
     * @Route("/{id}", name="api_task_update", format="json", methods={"PUT"}, requirements={"id"="\d+"})
     * @param int $id
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function update(int $id, Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $id, 'deleted' => false]);

        if (empty($task)) {
            return new JsonResponse(
                [
                    'code' => -1,
                    'errors' => ['Task not found']
                ]
            );
        }

        $form = $this->createForm(TaskType::class, $task, ['csrf_protection' => false, 'method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->serializeEntityToJsonResponse($task, 'task');
        } else {
            return new JsonResponse([
                'code' => -1,
                'errors' => explode("\n", trim((string) $form->getErrors(true)))
            ]);
        }
    }

    /**
     * @Route("/{id}", name="api_task_delete", format="json", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param int $id
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function delete(int $id, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $id, 'deleted' => false]);

        if (empty($task)) {
            return new JsonResponse([
                'code' => -1,
                'errors' => ['Task not found']
            ]);
        } else {
            $task->setDeleted(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return new JsonResponse([
                'code' => 0,
                'message' => 'Task has been deleted successfully'
            ]);
        }
    }
}
