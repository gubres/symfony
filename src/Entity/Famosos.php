<?php

namespace App\Entity;

use App\Repository\FamososRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: FamososRepository::class)]
#[ORM\HasLifecycleCallbacks]


class Famosos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "El campo no puede estar vacío")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z]+(\s[a-zA-Z]+)*$/",
        message: "El nombre debe ser alfabético"
    )]
     private ?string $nombre = null;

     #[ORM\Column(length: 30)]
     #[Assert\NotBlank(message: "El campo no puede estar vacío")]
     #[Assert\Regex(
         pattern: "/^[a-zA-Z]+(\s[a-zA-Z]+)*$/",
         message: "El apellido debe ser alfabético"
     )]
      private ?string $apellido = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "El campo no puede estar vacío")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z]+(\s[a-zA-Z]+)*$/",
        message: "La profesion debe ser alfabético"
    )]

    private ?string $profesion = null;

    #[ORM\Column]
    private ?bool $eliminado = false;
    
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $modificado = null;
    
    #[ORM\Column]
    private ?DateTimeImmutable $creado = null;
    
      public function __construct()
    {
        $zonaHoraria = new DateTimeZone('Europe/Madrid'); 
        $this->creado = new DateTimeImmutable('now', $zonaHoraria);
        $this->modificado = new DateTime('now', $zonaHoraria);
        $this->eliminado = false;
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getProfesion(): ?string
    {
        return $this->profesion;
    }

    public function setProfesion(string $profesion): static
    {
        $this->profesion = $profesion;

        return $this;
    }

    public function getEliminado(): ?bool
    {
        return $this->eliminado;
    }

    public function setEliminado  (bool $eliminado): self
    {
        $this->eliminado = $eliminado;

        return $this;
    }

    public function getModificado(): ?DateTime
    {
        return $this->modificado;
    }

    public function setModificado ( DateTime $modificado): self
    {
        $this->modificado = $modificado;

        return $this;
    }

    public function getCreado(): ?DateTimeImmutable
    {
        return $this->creado;
    }


    public function setCreado (DateTimeImmutable $creado): self
    {
        $this->creado = $creado;

        return $this;
    }
    
     /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        if (!$this->creado) {
            
            $this->creado = new DateTimeImmutable();
        }
        if (!$this->modificado) {
            $this->modificado = new DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->modificado = new DateTime();
    }
}
