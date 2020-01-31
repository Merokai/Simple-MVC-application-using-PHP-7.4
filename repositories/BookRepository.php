<?php


namespace Repositories;

use Core\Database;
use Exception;
use Models\Book;

class BookRepository
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): BookRepository
    {
        if (!isset($instance)) {
            BookRepository::$instance = new BookRepository();
        }
        return BookRepository::$instance;
    }

    public function findAll(): array
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("SELECT books.id, books.title, books.author, users.displayName, users.email FROM books, users WHERE books.owner = users.email");
        $req->execute();
        $res = $req->fetchAll();
        $books = [];
        foreach ($res as $row) {
            $book = new Book();
            $book->setId($row[0]);
            $book->setTitle($row[1]);
            $book->setAuthor($row[2]);
            $book->setOwner([
                "displayName" => $row[3],
                "email" => $row[4]
            ]);
            array_push($books, $book);
        }
        return $books;
    }

    public function createBook(Book $book): bool
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("INSERT INTO books(title, author, owner) VALUES(:title, :author, :owner)");
        $req->bindParam(":title", $book->getTitle());
        $req->bindParam(":author", $book->getAuthor());
        $req->bindParam(":owner", $book->getOwner()["email"]);
        return $req->execute();
    }

    public function updateBook(Book $book): bool
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("UPDAte books SET title=:title, author=:author WHERE id=:id");
        $req->bindParam(":title", $book->getTitle());
        $req->bindParam(":author", $book->getAuthor());
        $req->bindParam(":id", $book->getId());
        return $req->execute();
    }

    public function findBookById(int $id): Book
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("SELECT books.id, books.title, books.author, users.displayName, users.email FROM books, users WHERE books.owner LIKE users.email AND  books.id = :id");
        $req->bindParam(":id", $id);
        if ($req->execute() && $req->rowCount() == 1) {
            $row = $req->fetch();
            $book = new Book();
            $book->setId($row[0]);
            $book->setTitle($row[1]);
            $book->setAuthor($row[2]);
            $book->setOwner([
                "displayName" => $row[3],
                "email" => $row[4]
            ]);
            return $book;
        }
        throw new Exception("404 - Not found");
    }

    public function deleteBookById(int $id): bool
    {
        $pdo = Database::getInstance();
        $req = $pdo->prepare("DELETE FROM books WHERE books.id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }
}