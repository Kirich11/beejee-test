<?php

namespace App\Repositories;

use App\DbManager;
use App\Models\Status;
use App\Models\Task;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Repository to load task data
 */
class TaskRepo
{
    /** @var EntityRepository $repository */
    private $repository;

    /** @var EntityManager $entityManager */
    private $entityManager;

    public function __construct()
    {
        $this->repository = DbManager::getRepository(Task::class);
        $this->entityManager = DbManager::createManager();
    }

    /**
     * @param int $limit limit value
     * @param int $offset offset value
     * @param string $sort fieldname for sorting
     * @param bool $desc sorting DESC or ASC
     * 
     * @return Paginator
     * 
     * Get paginated tasks
     */
    public function getPaginated(int $limit, int $offset, string $sort, bool $desc) : Paginator
    {
        $query = $this->repository
            ->createQueryBuilder('t')
            ->leftJoin('t.status', 's')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($sort == 'status') {
            $query = $query->orderBy("s.id", $desc ? 'DESC' : 'ASC');
        } else {
            $query = $query->orderBy("t.{$sort}", $desc ? 'DESC' : 'ASC');
        }

        $paginator = new Paginator($query, false);

        return $paginator;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $value
     * 
     * @return void
     * 
     * Creates task
     */
    public function createTask(string $name, string $email, string $value)
    {
        $task = new Task();
        $task->setName($name);
        $task->setEmail($email);
        $task->setValue($value);
        $date = new DateTime();        
        $task->setCreated($date);
        $task->setUpdated($date);

        $statusRepo = new StatusRepo();
        $statusNew = $statusRepo->getNew();
        $task->setStatus($statusNew);

        $this->entityManager->persist($statusNew);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    /**
     * @param Task $task
     * @param string $name
     * @param string $email
     * @param string $value
     * @param Status $status
     * 
     * @return void
     * 
     * Updates task
     */
    public function updateTask(Task $task, string $name, string $email, string $value, Status $status)
    {
        if ($this->checkIfFieldsChanged($task, $name, $email, $value, $status)) {
            $task->setName($name);
            $task->setEmail($email);
            $task->setValue($value);
            $date = new DateTime();        
            $task->setUpdated($date);
            $task->setStatus($status);

            $this->entityManager->persist($status);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        }
        
    }

    /**
     * @param int $id
     * 
     * @return Task|null
     * 
     * find task by id
     */
    public function findById(int $id) : ?Task
    {
        return $this->repository->find($id);
    }

    /**
     * @param Task $task
     * @param string $name
     * @param string $email
     * @param string $value
     * @param Status $status
     * 
     * @return bool
     * 
     * check if any field was changed to define whether we should update the entity
     */
    private function checkIfFieldsChanged(Task $task, string $name, string $email, string $value, Status $status): bool
    {
        $changed = false;

        if ($task->getName() != $name) {
            $changed = true;
        }

        if (!$changed && $task->getEmail() != $email) {
            $changed = true;
        }

        if (!$changed && $task->getValue() != $value) {
            $changed = true;
        }

        if (!$changed && $task->getStatus()->getId() != $status->getId()) {
            $changed = true;
        }

        return $changed;
    }
}
