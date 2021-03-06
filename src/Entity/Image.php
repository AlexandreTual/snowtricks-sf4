<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\File(mimeTypes={ "image/jpeg" , "image/png" , "image/tiff" , "image/svg+xml"})
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="10", minMessage="media.length.caption.tooShort")
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="images")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $trick;

    /**
     * @ORM\Column(type="boolean")
     */
    private $coverImage;

    public function __construct()
    {
        $this->coverImage = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getLinkPath()
    {
        return '/uploads/images/' . $this->link;
    }

    public function setLink($link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getCoverImage(): ?bool
    {
        return $this->coverImage;
    }

    public function setCoverImage(bool $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }
}
