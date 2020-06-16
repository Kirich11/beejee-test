<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users", indexes={@ORM\Index(name="user_fullname_idx", columns={"firstname","lastname"}),@ORM\Index(name="user_email_idx", columns={"email"})})
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $login;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user")
     */
    protected $tasks;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=true)
     */
    protected $role;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setLogin(string $login)
    {
        $this->login = $login;
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function setRole(Role $role)
    {
        $role->addUser($this);
        $this->role = $role;
    }

    public function setPassword(string $password)
    {
        $hashPass = password_hash($password, PASSWORD_DEFAULT);
        $this->password = $hashPass;
    }
}
