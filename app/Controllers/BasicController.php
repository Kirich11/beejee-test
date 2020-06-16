<?php

namespace App\Controllers;

use App\DbManager;
use App\Models\Role;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicController implements ControllerInterface
{
    protected $templates;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../../views');
        $this->templates = new \Twig\Environment($loader, [
            'cache' => __DIR__.'/../../storage/views/cache',
            'auto_reload' => true
        ]);
    }

    /**
     * @param mixed $data data to be sent
     * @return Response
     * 
     * creates response instance from data
     */
    protected function response($data) : Response
    {
        return new Response($data);
    }

    /**
     * @param string $path path to view file
     * @param array $data array with data to render
     * @return Response
     * 
     * render view
     */
    protected function view(string $path, array $data = []) : Response
    {
        $template = $this->templates->load("{$path}.html");

        return $this->response($template->render($data));
    }

    /**
     * @param Request $request
     * @return bool
     * 
     * Check if user is authorized
     */
    protected function isAuthorized(Request $request) : bool
    {
        $session = $request->getSession();

        if ($session->has('login')) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool
     * 
     * Check if user is admin
     */
    protected function isAdmin(Request $request) : bool
    {
        $session = $request->getSession();

        if ($session->has('login')) {
            $userRepo = DbManager::getRepository(User::class);
            /** @var User $user */
            $user = $userRepo->findOneBy(['login' => $session->get('login')]);

            if (!empty($user)) {
                return $user->getRole()->getId() === Role::ADMIN;
            }
        }


        return false;
    }
}
