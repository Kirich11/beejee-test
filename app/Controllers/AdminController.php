<?php

namespace App\Controllers;

use App\Repositories\TaskRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends BasicController
{
    /**
     * @param Request $request
     * @return Response
     * 
     * view index page with admin role
     */
    public function index(Request $request) : Response
    {
        if ($this->isAdmin($request)) {
            $page = $request->get('page') ?? 1;
            $sort = $request->get('sort') ?? 'id';
            $desc = $request->get('desc') === 'false' ? false : true;

            $limit = 3;
            $offset = $limit * ($page - 1);

            $taskRepo = new TaskRepo();
            $paginatedTasks = $taskRepo->getPaginated($limit, $offset, $sort, $desc);
            $session = $request->getSession();
            $errors = $session->get('errors');
            $successes = $session->get('successes');

            $session->remove('errors');
            $session->remove('successes');

            return $this->view('admin.index', [
                'data' => $paginatedTasks,
                'count' => $paginatedTasks->count(),
                'errors' => $errors,
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
        
        return new RedirectResponse('/users/login');
    }
}
