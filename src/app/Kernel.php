<?php

namespace App;

use App\Controllers\ControllerInterface;
use App\Exceptions\NotFoundException;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Throwable;

/**
 * App entrance and HTTP request handler
 */
class Kernel
{
    protected $routes;
    protected $session;

    public function __construct()
    {
        $this->loadRoutes();
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../.env');
    }

    /**
     * Handle incoming request
     *
     * @param Request $request
     * @return Response
     *
     */
    public function handle(Request $request): Response
    {
        $this->startSession($request);

        $requestMethod = $request->server->get('REQUEST_METHOD');
        $uri = $request->server->get('REQUEST_URI');
        
        /* handle request path */
        $pathParts = explode('/', $uri);

        /* Check path for model name */
        $modelName = explode("?", $pathParts[1])[0];
        if (empty($modelName)) {
            return new RedirectResponse('/tasks');
        }

        /* Check path for model action */
        $controllerUri = explode("?", $pathParts[2])[0];
        if (empty($controllerUri)) {
            $controllerUri = '/';
        }

        /* Load controller and find method in routes config */
        $controller = $this->loadController($modelName);
        $controllerMethod = $this->loadControllerMethod($modelName, $requestMethod, $controllerUri);

        return $controller->$controllerMethod($request);
    }

    /**
     * Loads controller instance for further request handling
     *
     * @param string $modelname
     * @return ControllerInterface
     *
     * @throws NotFoundException
     */
    private function loadController(string $modelname): ControllerInterface
    {
        $model = ucfirst($modelname);
        $controllersNamespace = "App\\Controllers\\";
        $controllerPath = $controllersNamespace.$model.'Controller';

        if (class_exists($controllerPath)) {
            return new $controllerPath();
        }

        throw new NotFoundException("Cannot load controller for model: {$model}");
    }

    /**
     * Initialize route config
     *
     * @throws NotFoundException
     */
    private function loadRoutes(): void
    {
        $routeConfigPath = __DIR__.'/../config/routes.php';

        if (file_exists($routeConfigPath)) {
            $config = include $routeConfigPath;
        } else {
            throw new NotFoundException('Cannot load routes config');
        }

        $this->routes = $config;
    }

    /**
     * Load controller method based on request URI
     *
     * @param string $modelName model name called for action
     * @param string $requestMethod HTTP request method
     * @param string $controllerUri action called for selected model
     * 
     * @return string
     * @throws NotFoundException
     */
    private function loadControllerMethod(string $modelName, string $requestMethod, string $controllerUri): string
    {
        $modelRoutes = $this->routes[$modelName];
        if (empty($modelRoutes)) {
            throw new NotFoundException("Cannot load routes for model: {$modelName}");
        }

        $modelRoutesByMethod = $modelRoutes[$requestMethod];
        if (empty($modelRoutesByMethod)) {
            throw new NotFoundException("{$modelName} do not have {$requestMethod} methods");
        }

        $controllerMethod = $modelRoutesByMethod[$controllerUri];
        if (empty($controllerMethod)) {
            throw new NotFoundException("{$modelName} do not have {$controllerUri} uri");
        }

        return $controllerMethod;
    }

    /**
     * Start or regenerate session based on request
     * 
     * @param Request $request
     * @return void
     */
    private function startSession(Request $request)
    {
        try {
            $session = $request->getSession();
            $session->migrate();
        } catch (Throwable $t) {
            $session = new Session(new NativeSessionStorage(), new AttributeBag());
            $request->setSession($session);
        }
    }
}
