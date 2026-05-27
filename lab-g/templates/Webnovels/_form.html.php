<?php
/** @var \App\Model\Webnovels $Webnovels */
/** @var \App\Service\Router $router */
?>

<form method="post">
    <div>
        <label for="Tytul">Title:</label>
        <input type="text" id="Tytul" name="Webnovels[Tytul]" value="<?= $Webnovels->getTytul() ?? '' ?>">
    </div>
    <div>
        <label for="Autor">Author:</label>
        <input type="text" id="Autor" name="Webnovels[Autor]" value="<?= $Webnovels->getAutor() ?? '' ?>">
    </div>
    <div>
        <label for="Opis">Description:</label>
        <textarea id="Opis" name="Webnovels[Opis]"><?= $Webnovels->getOpis() ?? '' ?></textarea>
    </div>
    <button type="submit">Save</button>
</form>
