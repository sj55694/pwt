<?php
/** @var \App\Model\Webnovels $Webnovels */
/** @var \App\Service\Router $router */

$title = 'Webnovel: ' . $Webnovels->getTytul();
$bodyClass = 'show';

ob_start(); ?>
    <h1><?= $Webnovels->getTytul() ?></h1>

    <p><strong>Author:</strong> <?= $Webnovels->getAutor() ?></p>
    <p><strong>Description:</strong></p>
    <p><?= nl2br($Webnovels->getOpis()) ?></p>

    <ul class="action-list">
        <li><a href="<?= $router->generatePath('Webnovels-edit', ['id' => $Webnovels->getId()]) ?>">Edit</a></li>
        <li><a href="<?= $router->generatePath('Webnovels-delete', ['id' => $Webnovels->getId()]) ?>">Delete</a></li>
        <li><a href="<?= $router->generatePath('Webnovels-index') ?>">Back to list</a></li>
    </ul>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';