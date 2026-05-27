<?php
/** @var \App\Model\Webnovels[] $Webnovels */
/** @var \App\Service\Router $router */

$title = 'Webnovels List';
$bodyClass = 'index';

ob_start(); ?>
    <h1>Webnovels List</h1>

    <a href="<?= $router->generatePath('Webnovels-create') ?>">Create new</a>

    <ul class="index-list">
        <?php foreach ($Webnovels as $webnovel): ?>
            <li><h3><?= $webnovel->getTytul() ?></h3>
                <p><strong>Author:</strong> <?= $webnovel->getAutor() ?></p>
                <ul class="action-list">
                    <li><a href="<?= $router->generatePath('Webnovels-show', ['id' => $webnovel->getId()]) ?>">Details</a></li>
                    <li><a href="<?= $router->generatePath('Webnovels-edit', ['id' => $webnovel->getId()]) ?>">Edit</a></li>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';