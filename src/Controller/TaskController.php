<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * Crée une nouvelle tâche à partir des données envoyées en JSON
     * Requiert au minimum un champ `title`
     */
    #[Route('/tasks', name: 'create_task', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie que le titre est bien présent
        if (!isset($data['title'])) {
            return new JsonResponse(['message' => 'Le titre est requis'], 400);
        }

        // Création de la nouvelle tâche
        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? null);
        $task->setStatus(isset($data['status']) ? (bool) $data['status'] : false);

        // Sauvegarde en base
        $em->persist($task);
        $em->flush();

        // Retourne la réponse JSON avec les données de la tâche créée
        return new JsonResponse([
            'message' => 'Tâche créée avec succès',
            'data' => [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ]
        ], 201);
    }

    /**
     * Récupère et retourne la liste de toutes les tâches
     */
    #[Route('/tasks', name: 'list_tasks', methods: ['GET'])]
    public function listTasks(EntityManagerInterface $em): JsonResponse
    {
        $tasks = $em->getRepository(Task::class)->findAll();

        // Formate les données des tâches pour le JSON
        $data = array_map(function (Task $task) {
            return [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ];
        }, $tasks);

        return new JsonResponse([
            'message' => 'Liste des tâches récupérée',
            'data' => $data,
        ], 200);
    }

    /**
     * Récupère une tâche précise via son ID
     */
    #[Route('/tasks/{id}', name: 'get_task', methods: ['GET'])]
    public function getTask(int $id, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], 404);
        }

        return new JsonResponse([
            'message' => 'Tâche récupérée',
            'data' => [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ]
        ], 200);
    }

    /**
     * Met à jour uniquement le statut (status) d'une tâche via son ID
     * Le statut doit être fourni dans le body JSON : { "status": true }
     */
    #[Route('/tasks/{id}', name: 'update_task_status', methods: ['PUT'])]
    public function updateTaskStatus(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['status'])) {
            return new JsonResponse(['message' => 'Le statut est requis'], 400);
        }

        // Mise à jour du statut
        $task->setStatus((bool) $data['status']);
        $em->flush();

        return new JsonResponse([
            'message' => 'Statut de la tâche mis à jour',
            'data' => [
                'id' => $task->getId(),
                'status' => $task->getStatus(),
            ]
        ], 200);
    }

    /**
     * Met à jour les champs d'une tâche via son ID
     * Les champs modifiables : title, description et status (partiels via PATCH)
     */
    #[Route('/tasks/{id}', name: 'edit_task', methods: ['PATCH'])]
    public function editTask(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        // Mise à jour des champs si présents dans la requête
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $task->setStatus((bool) $data['status']);
        }

        $em->flush();

        return new JsonResponse([
            'message' => 'Tâche mise à jour',
            'data' => [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ]
        ], 200);
    }

    /**
     * Supprime une tâche via son ID
     */
    #[Route('/tasks/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(int $id, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], 404);
        }

        $em->remove($task);
        $em->flush();

        return new JsonResponse(['message' => 'Tâche supprimée'], 200);
    }
}
