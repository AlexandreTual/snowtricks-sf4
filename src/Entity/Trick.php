<?php

namespace App\Entity;

use App\Core\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name", message="trick.uniqueEntity")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min= 20,
     *     minMessage="trick.length.introduction.tooShort")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *     min= 30,
     *     minMessage="trick.length.description.tooShort")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(message = "url.notValid")
     */
    private $coverImage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Media", mappedBy="trick", cascade="all", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $Media;

    public function __construct()
    {
        $this->Media = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function initialiseSlug()
    {
        $this->slug = Utils::slugMaker($this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;
        
        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->Media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->Media->contains($medium)) {
            $this->Media[] = $medium;
            $medium->setTrick($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->Media->contains($medium)) {
            $this->Media->removeElement($medium);
            // set the owning side to null (unless already changed)
            if ($medium->getTrick() === $this) {
                $medium->setTrick(null);
            }
        }

        return $this;
    }

}
