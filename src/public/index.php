<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

try {
    $request = Request::createFromGlobals();

    $app = new Kernel();

    $response = $app->handle($request);

    return $response->send();
} catch (\Throwable $t) {
    echo $t->getMessage();
}