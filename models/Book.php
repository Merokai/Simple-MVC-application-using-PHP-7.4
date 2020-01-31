<?php


namespace Models;


class Book
{
    public $id, $title, $author, $owner;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setOwner(array $owner): void
    {
        $this->owner = $owner;
    }

    public function getOwner(): array
    {
        return $this->owner;
    }
}