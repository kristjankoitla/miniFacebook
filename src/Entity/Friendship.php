<?php

namespace App\Entity;

use App\Repository\FriendshipRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FriendshipRepository::class)
 */
class Friendship
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friendship")
     */
    private $initiator;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friendship")
     */
    private $receiver;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    public function setInitiator($initiator): void
    {
        $this->initiator = $initiator;
    }

    public function getReceiver()
    {
        return $this->receiver;
    }

    public function setReceiver($receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

}
