<?php

namespace App\Controller;

use App\Entity\Task;
use App\Event\TaskCreatedEvent;
use App\Event\TaskDeletedEvent;
use App\Event\TaskUpdatedEvent;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tasks")
 */
class TaskController extends AbstractController
{
    const PAGE_RESULTS = 20;

    /**
     * @Route("/", name="task_index", methods={"GET"})
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return Response
     */
    public function index(Request $request, TaskRepository $taskRepository): Response
    {
        $page = $request->query->get('page', 1);
        $tasks = $taskRepository->findAllPaginated($page);

        return $this->render('task/index.html.twig', [
            'total' => $tasks->count(),
            'limit' => self::PAGE_RESULTS,
            'maxPages' => ceil($tasks->count() / self::PAGE_RESULTS),
            'page' => (int) $page,
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     */
    public function new(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $event = new TaskCreatedEvent($task);
            $dispatcher->dispatch($event, TaskCreatedEvent::NAME);

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     * @param Task $task
     * @return Response
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Task $task
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     */
    public function edit(Request $request, Task $task, EventDispatcherInterface $dispatcher): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $event = new TaskUpdatedEvent($task);
            $dispatcher->dispatch($event, TaskUpdatedEvent::NAME);

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods={"DELETE"})
     * @param Request $request
     * @param Task $task
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     */
    public function delete(Request $request, Task $task, EventDispatcherInterface $dispatcher): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();

            $event = new TaskDeletedEvent($task);
            $dispatcher->dispatch($event, TaskDeletedEvent::NAME);
        }

        return $this->redirectToRoute('task_index');
    }
}
