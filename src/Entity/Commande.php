<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type("datetime")]
    #[Assert\GreaterThanOrEqual("today")]
    private $dt_heure_depart;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type("datetime")]
    #[Assert\GreaterThan(propertyPath: "dt_heure_depart")] // Permet d'éviter d'avoir des dates négatives 
    private $dt_heure_fin;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThan(value:30)]
    private $prix_total;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type("datetime")]
    private $dt_enregistrement;

    #[ORM\ManyToOne(targetEntity:Vehicule::class, inversedBy:"commandes", cascade:["persist"])]
    private $vehicule;

    #[ORM\ManyToOne(targetEntity:User::class, inversedBy:"commandes", cascade:["persist"])]
    private $user;

    public function __construct(){

        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime();
        $now->setTimezone($tz);
        $this->setDtEnregistrement($now); 

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDtHeureDepart(): ?\DateTimeInterface
    {
        return $this->dt_heure_depart;
    }

    public function setDtHeureDepart(\DateTimeInterface $dt_heure_depart): self
    {
        $this->dt_heure_depart = $dt_heure_depart;

        return $this;
    }

    public function getDtHeureFin(): ?\DateTimeInterface
    {
        return $this->dt_heure_fin;
    }

    public function setDtHeureFin(\DateTimeInterface $dt_heure_fin): self
    {
        $this->dt_heure_fin = $dt_heure_fin;

        return $this;
    }

    public function getPrixTotal(): ?int
    {
        return $this->prix_total;
    }

    public function setPrixTotal(int $prix_total): self
    {
        $this->prix_total = $prix_total;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getDtEnregistrement(): ?\DateTimeInterface
    {
        return $this->dt_enregistrement;
    }

    public function setDtEnregistrement(\DateTimeInterface $dt_enregistrement): self
    {
        $this->dt_enregistrement = $dt_enregistrement;

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): self
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
