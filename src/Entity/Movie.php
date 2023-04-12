<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $tmdbId = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalLanguage = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $overview = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(length: 1023, nullable: true)]
    private ?string $posterPath = null;

    #[ORM\Column(nullable: true)]
    private ?int $runtime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $releaseStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imdbId = null;

    #[Ignore]
    #[ORM\ManyToMany(targetEntity: WatchList::class, mappedBy: 'movies')]
    private Collection $watchLists;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: UserMovieMeta::class)]
    private Collection $userMovieMetas;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $directors = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $cast = [];

    public function __construct()
    {
        $this->watchLists = new ArrayCollection();
        $this->userMovieMetas = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(int $tmdbId): self
    {
        $this->tmdbId = $tmdbId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getOriginalTitle(): ?string
    {
        return $this->originalTitle;
    }

    public function setOriginalTitle(?string $originalTitle): self
    {
        $this->originalTitle = $originalTitle;

        return $this;
    }

    public function getOriginalLanguage(): ?string
    {
        return $this->originalLanguage;
    }

    public function setOriginalLanguage(?string $originalLanguage): self
    {
        $this->originalLanguage = $originalLanguage;

        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): self
    {
        $this->overview = $overview;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    public function setPosterPath(?string $posterPath): self
    {
        $this->posterPath = $posterPath;

        return $this;
    }

    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    public function setRuntime(?int $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    public function getReleaseStatus(): ?string
    {
        return $this->releaseStatus;
    }

    public function setReleaseStatus(?string $releaseStatus): self
    {
        $this->releaseStatus = $releaseStatus;

        return $this;
    }

    public function getImdbId(): ?string
    {
        return $this->imdbId;
    }

    public function setImdbId(?string $imdbId): self
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * @return Collection<int, WatchList>
     */
    public function getWatchLists(): Collection
    {
        return $this->watchLists;
    }

    public function addWatchList(WatchList $watchList): self
    {
        if (!$this->watchLists->contains($watchList)) {
            $this->watchLists->add($watchList);
            $watchList->addMovie($this);
        }

        return $this;
    }

    public function removeWatchList(WatchList $watchList): self
    {
        if ($this->watchLists->removeElement($watchList)) {
            $watchList->removeMovie($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, UserMovieMeta>
     */
    public function getUserMovieMetas(): Collection
    {
        return $this->userMovieMetas;
    }

    public function addUserMovieMeta(UserMovieMeta $userMovieMeta): self
    {
        if (!$this->userMovieMetas->contains($userMovieMeta)) {
            $this->userMovieMetas->add($userMovieMeta);
            $userMovieMeta->setMovie($this);
        }

        return $this;
    }

    public function removeUserMovieMeta(UserMovieMeta $userMovieMeta): self
    {
        if ($this->userMovieMetas->removeElement($userMovieMeta)) {
            // set the owning side to null (unless already changed)
            if ($userMovieMeta->getMovie() === $this) {
                $userMovieMeta->setMovie(null);
            }
        }

        return $this;
    }

    public function getDirectors(): array
    {
        return $this->directors;
    }

    public function setDirectors(?array $directors): self
    {
        $this->directors = $directors;

        return $this;
    }

    public function getCast(): array
    {
        return $this->cast;
    }

    public function setCast(?array $cast): self
    {
        $this->cast = $cast;

        return $this;
    }
}
