<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Controller\UploadImageAction;
/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 *
 * */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="url")
     * @Assert\NotNull()
     */
    private $file;
    /**
     * @ORM\Column(nullable=true)
     */
    private $filename;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $uploaddirectory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alternative_text;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="imageId")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUploaddirectory()
    {
        return $this->uploaddirectory;
    }

    /**
     * @param mixed $uploaddirectory
     */
    public function setUploaddirectory($uploaddirectory): void
    {
        $this->uploaddirectory = $uploaddirectory;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getFile()
    {
        return $this->file;
    }
    public function setFile($file)
    {
        $this->file = $file;
    }
    public function getFileName()
    {
        return $this->filename;
    }
    public function setFileName($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAlternativeText()
    {
        return $this->alternative_text;
    }


    public function setAlternativeText($alternative_text): self
    {
        $this->alternative_text = $alternative_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }





    public function __toString()
    {
        return $this->id;
    }
}