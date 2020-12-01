<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
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

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $text;

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

    public function getText()
    {
        return $this->text;
    }

    public function setText($text): void
    {
        $this->text = $text;
    }

}
