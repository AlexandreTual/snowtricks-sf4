<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *      pattern = "#^(https://)((w{3}\.youtube\.com/watch\?v=)|(youtu(.be/)))[a-zA-Z0-9]+$#",
     *      message = "Cette url n'est pas valide veuillez cliquer sur Info pour plus d'information !"
     * )
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10, minMessage="media.length.caption.tooShort")
     */
    private $caption;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="Media")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $patternYoutube1 = '#https://www\.youtube\.com/watch\?v=#';
        $replacementYoutube = 'https://www.youtube.com/embed/';
        $this->link = preg_replace($patternYoutube1, $replacementYoutube, $link) ;
        if ('image' == $this->type ) {
            $this->link = $link;
        }
        if ('video' == $this->type) {
            $patternYoutube1 = '#https://www\.youtube\.com/watch\?v=#';
            $patternYoutube2 = '#https://youtu\.be/#';
            $patternYoutube3 = '#https://www\.youtube\.com/embed/#';
            $replacementYoutube = 'https://www.youtube.com/embed/';
            $patternDayli1 = '#https://dai\.ly/#';
            $patternDayli2 ='#https://www\.dailymotion\.com/video/#';
            $patternDayli3 ='#https://www.dailymotion.com/embed/video/#';
            $replacementDayli = 'https://www.dailymotion.com/embed/video/';
    
            if (preg_match($patternYoutube3, $link)) {
                $this->link = $link;
            } elseif (preg_match($patternDayli3, $link)){
                $this->link = $link;
            } elseif (preg_match($patternYoutube1, $link)) {
                $this->link = preg_replace($patternYoutube1, $replacementYoutube, $link);
            } elseif (preg_match($patternYoutube2, $link)) {
                $this->link = preg_replace($patternYoutube2, $replacementYoutube, $link);
            } elseif (preg_match($patternDayli1, $link)) {
                $this->link = preg_replace($patternDayli1, $replacementDayli, $link);
            } elseif (preg_match($patternDayli2, $link)) {
                $this->link = preg_replace($patternDayli2, $replacementDayli, $link);
            }
        }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
}
