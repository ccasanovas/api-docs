<?php
declare(strict_types=1);

use App\Application\Actions\Documents\AddDocumentsAction;
use App\Application\Actions\Documents\DeleteDocumentsAction;
use App\Application\Actions\Documents\ListDocumentsAction;
use App\Application\Actions\Documents\ViewDocumentsAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/documents', function (Group $group) {
        $group->get('', ListDocumentsAction::class);
        $group->get('/{searchParam}/{arg}', ViewDocumentsAction::class);
        $group->post('/upload', AddDocumentsAction::class);
        $group->post('/delete/{fileName}', DeleteDocumentsAction::class);
    });

};
