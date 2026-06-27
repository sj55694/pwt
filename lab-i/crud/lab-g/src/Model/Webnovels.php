<?php
namespace App\Model;

use App\Service\Config;

class Webnovels
{
    private ?int $id = null;
    private ?string $Tytul = null;
    private ?string $Autor = null;
    private ?string $Opis = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Webnovels
    {
        $this->id = $id;

        return $this;
    }

    public function getTytul(): ?string
    {
        return $this->Tytul;
    }

    public function setTytul(?string $Tytul): Webnovels
    {
        $this->Tytul = $Tytul;

        return $this;
    }
    public function getOpis(): ?string
    {
        return $this->Opis;
    }

    public function setOpis(?string $Opis): Webnovels
    {
        $this->Opis = $Opis;

        return $this;
    }

    public function getAutor(): ?string
    {
        return $this->Autor;
    }

    public function setAutor(?string $Autor): Webnovels
    {
        $this->Autor = $Autor;

        return $this;
    }

    public static function fromArray($array): Webnovels
    {
        $Webnovels = new self();
        $Webnovels->fill($array);

        return $Webnovels;
    }

    public function fill($array): Webnovels
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['Tytul'])) {
            $this->setTytul($array['Tytul']);
        }
        if (isset($array['Autor'])) {
            $this->setAutor($array['Autor']);
        }
        if (isset($array['Opis'])) {
            $this->setOpis($array['Opis']);
        }

        return $this;
    }

    public static function findAll(): array
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM Webnovels';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $Webnovelss = [];
        $WebnovelssArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($WebnovelssArray as $WebnovelsArray) {
            $Webnovelss[] = self::fromArray($WebnovelsArray);
        }

        return $Webnovelss;
    }

    public static function find($id): ?Webnovels
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM Webnovels WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $WebnovelsArray = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $WebnovelsArray) {
            return null;
        }
        $Webnovels = Webnovels::fromArray($WebnovelsArray);

        return $Webnovels;
    }

    public function save(): void
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        if (! $this->getId()) {
            $sql = "INSERT INTO Webnovels (Tytul, Autor, Opis) VALUES (:Tytul, :Autor,:Opis)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                'Tytul' => $this->getTytul(),
                'Autor' => $this->getAutor(),
                'Opis' => $this->getOpis(),
            ]);

            $this->setId($pdo->lastInsertId());
        } else {
            $sql = "UPDATE Webnovels SET Tytul = :Tytul, Autor = :Autor,Opis=:Opis WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':Tytul' => $this->getTytul(),
                ':Autor' => $this->getAutor(),
                ':id' => $this->getId(),
                'Opis' => $this->getOpis(),
            ]);
        }
    }

    public function delete(): void
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = "DELETE FROM Webnovels WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setTytul(null);
        $this->setAutor(null);
        $this->setOpis(null);
    }
}
