<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    /**
     * Identifiant unique de la tâche (clé primaire auto-générée)
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['task:read'])]
    private ?int $id = null;

    /**
     * Titre de la tâche
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['task:read'])]
    private string $title;

    /**
     * Description optionnelle de la tâche
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['task:read'])]
    private ?string $description = null;

    /**
     * Statut de la tâche (true = terminée, false = en cours)
     */
    #[ORM\Column(type: 'boolean')]
    #[Groups(['task:read'])]
    private bool $status = false;

    /**
     * Récupère l'ID de la tâche
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le titre de la tâche
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Modifie le titre de la tâche
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Récupère la description de la tâche (ou null si absente)
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Modifie la description de la tâche
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Récupère le statut de la tâche
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * Modifie le statut de la tâche
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }
}
