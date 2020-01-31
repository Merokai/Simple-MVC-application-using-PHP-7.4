<?php


namespace Controllers;


use Core\Error;
use Core\ViewLoader;
use Exception;
use Models\Book;
use Repositories\BookRepository;
use Utils\ParameterValidator;
use Utils\RoleValidator;

class BookController
{
    public function getIndex()
    {
        $books = BookRepository::getInstance()->findAll();
        ViewLoader::getInstance()->render(
            'Books/Books.html',
            [
                "books" => $books
            ]
        );
    }

    public function getAdd()
    {
        // Accès interdit aux utilsateurs non-authentifiés
        RoleValidator::validateRoleOrDie(["visitor", "admin"]);

        $books = BookRepository::getInstance()->findAll();
        ViewLoader::getInstance()->render(
            'Books/Create.html',
            [
            ]
        );
    }

    public function postAdd(array $params)
    {
        // Accès interdit aux utilsateurs non-authentifiés
        RoleValidator::validateRoleOrDie(["visitor", "admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['title', 'author'], $params);

        $book = new Book();
        $book->setTitle($params["title"]);
        $book->setAuthor($params["author"]);
        $book->setOwner(["email" => $_SESSION["user"]["email"]]);
        if (BookRepository::getInstance()->createBook($book)) {
            header("Location: /book");
        } else {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(500, "Impossible d'ajouter le livre.")]
            );
        }
    }

    public function getDelete(array $params): void
    {
        // Accès interdit aux utilsateurs non-authentifiés
        RoleValidator::validateRoleOrDie(["visitor", "admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['id'], $params);

        $id = intval($params['id']);
        try {
            $book = BookRepository::getInstance()->findBookById($params["id"]);
            if ($_SESSION["user"]["email"] == $book->getOwner()["email"] || $_SESSION["user"]["role"] == "admin") {
                BookRepository::getInstance()->deleteBookById($id);
                header("Location: /book");
            }
        } catch (Exception $ex) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(500, "Impossible de supprimer le livre.")]
            );
        }
    }

    public function getEdit(array $params): void
    {
        // Accès interdit aux utilsateurs non-authentifiés
        RoleValidator::validateRoleOrDie(["visitor", "admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['id'], $params);

        try {
            $book = BookRepository::getInstance()->findBookById($params["id"]);
            ViewLoader::getInstance()->render(
                'Books/Create.html',
                [
                    'user' => $_SESSION["user"],
                    'book' => $book,
                    'edit' => true
                ]
            );
        } catch (Exception $ex) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(404, "Impossible de trouver le livre.")]
            );
        }
    }

    public function postEdit(array $params)
    {
        // Accès interdit aux utilsateurs non-authentifiés
        RoleValidator::validateRoleOrDie(["visitor", "admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['title', 'author', 'id'], $params);

        $book = BookRepository::getInstance()->findBookById($params['id']);
        if (!$book->getOwner() == $_SESSION["user"]["email"]) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(403, "Seul le propriétaire d'un livre peut le modifier.")]
            );
        }
        $book->setTitle($params["title"]);
        $book->setAuthor($params["author"]);
        if (BookRepository::getInstance()->updateBook($book)) {
            header("Location: /book");
        } else {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(500, "Impossible de modifier le livre.")]
            );
        }
    }
}