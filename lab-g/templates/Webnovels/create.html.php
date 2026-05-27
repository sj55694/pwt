<?php
/** @var \App\Model\Webnovels $Webnovels */
/** @var \App\Service\Router $router */

$title = 'Create Webnovel';
$bodyClass = 'create';

ob_start(); ?>
    <h1>Create New Webnovel</h1>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';