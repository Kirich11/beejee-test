<?php

namespace App\Controllers;

use App\Repositories\StatusRepo;
use App\Repositories\TaskRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class TasksController extends BasicController
{
    /**
     * @param Request $request
     * @return Response
     * 
     * view index page as a non-authorized user
     */
    public function index(Request $request) : Response
    {
        $page = $request->get('page') ?? 1;
        $sort = $request->get('sort') ?? 'id';
        $desc = $request->get('desc') === 'false' ? false : true;

        $limit = 3;
        $offset = $limit * ($page - 1);

        $taskRepo = new TaskRepo();
        $paginatedTasks = $taskRepo->getPaginated($limit, $offset, $sort, $desc);

        $session = $request->getSession();
        $successes = $session->get('successes');
        $session->remove('successes');

        return $this->view('index', [
            'data' => $paginatedTasks,
            'isAdmin' => $this->isAdmin($request),
            'successes' => $successes,
            'lastPage' => ceil($paginatedTasks->count() / $limit),
            'currentPage' => $page,
            'desc' => $desc ? 'false' : 'true',
            'queryString' => http_build_query([
                'sort' => $sort,
                'desc' => $desc ? 'true' : 'false',
            ])
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * 
     * view create page for tasks
     */
    public function createPage(Request $request) : Response
    {
        return $this->view('tasks.create', [
            'error' => [],
            'isAdmin' => $this->isAdmin($request)
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * 
     * create task
     */
    public function create(Request $request) : Response
    {
        $name = filter_var($request->get('name'), FILTER_SANITIZE_STRING);
        $email = filter_var($request->get('email'), FILTER_SANITIZE_EMAIL);
        $value = filter_var($request->get('value'), FILTER_SANITIZE_STRING);

        /** @var Session $session */
        $session = $request->getSession();

        if (empty($name)) {
            $session->getFlashBag()->add('errors', 'Name must not be empty');
        }
        if (empty($email)) {
            $session->getFlashBag()->add('errors', 'Email must not be empty');
        }
        if (empty($value)) {
            $session->getFlashBag()->add('errors', 'Value must not be empty');
        }
                
        $errors = $session->getFlashBag()->get('errors');

        if (count($errors)) {
            return $this->view('tasks.create', [
                'errors' => $errors,
                'isAdmin' => $this->isAdmin($request)
            ]);
        }

        $taskRepo = new TaskRepo();
        $taskRepo->createTask($name, $email, $value);

        $session->set('successes', ['Task created!']);

        if ($this->isAdmin($request)) {
            return new RedirectResponse('/admin');
        }

        return new RedirectResponse('/tasks');
    }

    /**
     * @param Request $request
     * @return Response
     * 
     * update task
     */
    public function update(Request $request) : Response
    {
        if (!$this->isAdmin($request)) {
            return new RedirectResponse('/users/login');
        }

        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        $name = filter_var($request->get('name'), FILTER_SANITIZE_STRING);
        $email = filter_var(filter_var($request->get('email'), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        $value = filter_var($request->get('value'), FILTER_SANITIZE_STRING);
        $status = filter_var($request->get('status'), FILTER_VALIDATE_INT);

        $errors = [];
        if (empty($name)) {
            $errors[] = "Name must not be empty for id {$id}";
        }
        if (empty($email)) {
            $errors[] = "Email must be in correct format and not empty for id {$id}";
        }
        if (empty($value)) {
            $errors[] = "Text must not be empty for id {$id}";
        }

        $session = $request->getSession();

        if (count($errors)) {
            /** @var Session $session */
            $session->set('errors', $errors);

            if ($this->isAdmin($request)) {
                return new RedirectResponse('/admin');
            }
    
            return new RedirectResponse('/tasks');
        }

        $taskRepo = new TaskRepo();
        $statusRepo = new StatusRepo();

        if ($status === false) {
            $status = $statusRepo->getNew();
        } else {
            $status = $statusRepo->getDone();
        }

        $task = $taskRepo->findById($id);
        $taskRepo->updateTask($task, $name, $email, $value, $status);

        $session->set('successes', ['Task updated!']);

        if ($this->isAdmin($request)) {
            return new RedirectResponse('/admin');
        }

        return new RedirectResponse('/tasks');
    }
}
