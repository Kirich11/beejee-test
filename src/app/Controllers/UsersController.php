<?php

namespace App\Controllers;

use App\Repositories\UserRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class UsersController extends BasicController
{
    /**
     * @param Request $request
     * @return Response
     * 
     * view login page
     */
    public function loginPage(Request $request) : Response
    {
        return $this->view('login', [
            'error' => []
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * 
     * login action
     */
    public function login(Request $request) : Response
    {
        $login = filter_var($request->get('login'), FILTER_SANITIZE_STRING);
        $password = filter_var($request->get('password'), FILTER_SANITIZE_STRING);

        $userRepo = new UserRepo();
        $user = $userRepo->getByLogin($login);

        /** @var Session $session */
        $session = $request->getSession();

        if (!empty($user)) {
            if (password_verify($password, $user->getPassword())) {
                $session->set('login', $user->getLogin());
                $session->getFlashBag()->clear();

                return new RedirectResponse('/admin');
            }
        }

        $session->getFlashBag()->add('errors', 'Invalid credentials');
        $errors = $session->getFlashBag()->get('errors');

        return $this->view('login', [
            'errors' => $errors
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * 
     * logout action
     */
    public function logout(Request $request) : Response
    {
        $request->getSession()->remove('login');

        return new RedirectResponse('/tasks');
    }
}
