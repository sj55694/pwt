<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Webnovels;
use App\Service\Router;
use App\Service\Templating;

class WebnovelsController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $Webnovels = Webnovels::findAll();
        $html = $templating->render('Webnovels/index.html.php', [
            'Webnovels' => $Webnovels,
            'router' => $router,
        ]);
        return $html;
    }

    public function createAction(?array $requestWebnovels, Templating $templating, Router $router): ?string
    {
        if ($requestWebnovels) {
            $Webnovels = Webnovels::fromArray($requestWebnovels);
            // @todo missing validation
            $Webnovels->save();

            $path = $router->generatePath('Webnovels-index');
            $router->redirect($path);
            return null;
        } else {
            $Webnovels = new Webnovels();
        }

        $html = $templating->render('Webnovels/create.html.php', [
            'Webnovels' => $Webnovels,
            'router' => $router,
        ]);
        return $html;
    }

    public function editAction(int $WebnovelsId, ?array $requestWebnovels, Templating $templating, Router $router): ?string
    {
        $Webnovels = Webnovels::find($WebnovelsId);
        if (! $Webnovels) {
            throw new NotFoundException("Missing Webnovels with id $WebnovelsId");
        }

        if ($requestWebnovels) {
            $Webnovels->fill($requestWebnovels);
            // @todo missing validation
            $Webnovels->save();

            $path = $router->generatePath('Webnovels-index');
            $router->redirect($path);
            return null;
        }

        $html = $templating->render('Webnovels/edit.html.php', [
            'Webnovels' => $Webnovels,
            'router' => $router,
        ]);
        return $html;
    }

    public function showAction(int $WebnovelsId, Templating $templating, Router $router): ?string
    {
        $Webnovels = Webnovels::find($WebnovelsId);
        if (! $Webnovels) {
            throw new NotFoundException("Missing Webnovels with id $WebnovelsId");
        }

        $html = $templating->render('Webnovels/show.html.php', [
            'Webnovels' => $Webnovels,
            'router' => $router,
        ]);
        return $html;
    }

    public function deleteAction(int $WebnovelsId, Router $router): ?string
    {
        $Webnovels = Webnovels::find($WebnovelsId);
        if (! $Webnovels) {
            throw new NotFoundException("Missing Webnovels with id $WebnovelsId");
        }

        $Webnovels->delete();
        $path = $router->generatePath('Webnovels-index');
        $router->redirect($path);
        return null;
    }
}
