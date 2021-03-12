<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ImageRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image // implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("image")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("image")
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     *@Groups("share")
     */
    private $owner;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=10, nullable=true)
     * @Groups({"image","share"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=10, nullable=true)
     * @Groups({"image","share"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public;


    private $filePath;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"image","share"})
     */
    private $UploadedAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"image","share"})
     */
    private $originalName;

    /**
     * @ORM\ManyToMany(targetEntity=Album::class, mappedBy="image")
     */
    private Collection $albums;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="image")
     * @Groups("image")
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    public function __construct()
    {
        $this->albums = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return UploaderHelper::IMAGE_DIRECTORY.'/'.$this->getFilename();
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->UploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $UploadedAt): self
    {
        $this->UploadedAt = $UploadedAt;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * @return Collection|Album[]
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums[] = $album;
            $album->addImage($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            $album->removeImage($this);
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addImage($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeImage($this);
        }

        return $this;
    }

    /*public function jsonSerialize()
    {
        return [
            'originalName' => $this->originalName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'uploadedAt' => $this->UploadedAt,
            'tags' => $this->tags
        ];
    }*/

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }


}
