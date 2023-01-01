<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use App\Traits\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocieteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Societe
{
    use TimeStampTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nameSoc = null;

    #[ORM\OneToMany(mappedBy: 'societe', targetEntity: Personne::class)]
    private Collection $Personne;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Address = null;

    public function __construct()
    {
        $this->Personne = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameSoc(): ?string
    {
        return $this->nameSoc;
    }

    public function setNameSoc(?string $nameSoc): self
    {
        $this->nameSoc = $nameSoc;

        return $this;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPersonne(): Collection
    {
        return $this->Personne;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->Personne->contains($personne)) {
            $this->Personne->add($personne);
            $personne->setSociete($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->Personne->removeElement($personne)) {
            // set the owning side to null (unless already changed)
            if ($personne->getSociete() === $this) {
                $personne->setSociete(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nameSoc;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(?string $Address): self
    {
        $this->Address = $Address;

        return $this;
    }
}
