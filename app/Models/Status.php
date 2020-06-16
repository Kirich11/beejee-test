<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="statuses")
 */
class Status
{
    const STATE_NEW = 1;
    const STATE_DONE = 2;

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
     * @ORM\OneToMany(targetEntity="Task", mappedBy="status")
     */
    protected $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }
}
