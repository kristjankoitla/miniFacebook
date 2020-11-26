<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikeRepository::class)
 * @ORM\Table(name="`like`")
 */
class Like
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="like")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="like")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function setPost($post): void
    {
        $this->post = $post;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

}
