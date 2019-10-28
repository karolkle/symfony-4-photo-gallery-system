<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Entity\Image", inversedBy="comments")
     */
    private $imageId;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $body;

    public function getId()
    {
        return $this->id;
    }


    public function getImageId()
    {
        return $this->imageId;
    }


    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    }



    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;

    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

}
