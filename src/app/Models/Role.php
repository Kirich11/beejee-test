<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class Role
{
    const ADMIN = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $value;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="role")
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }
}
