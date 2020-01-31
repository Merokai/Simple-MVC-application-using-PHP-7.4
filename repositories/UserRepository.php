<?php


namespace Repositories;

use Core\Database;
use Exception;
use Models\User;

class UserRepository
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): UserRepository
    {
        if (!isset($instance)) {
            UserRepository::$instance = new UserRepository();
        }
        return UserRepository::$instance;
    }

    public function findByEmail($email): User
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("SELECT * FROM users WHERE email LIKE :email");
        $req->bindParam(":email", $email);
        $req->execute();
        $res = $req->fetchObject(User::class);
        if (!$res) {
            throw new Exception("Utilisateur introuvable", 404);
        }
        return $res;
    }

    public function findAll(): array
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("SELECT * FROM users");
        $req->execute();
        $res = $req->fetchAll();
        $users = [];
        foreach ($res as $row) {
            $user = new User();
            $user->displayName = $row['displayName'];
            $user->email = $row['email'];
            $user->role = $row['role'];
            $user->password = "##########";
            array_push($users, $user);
        }
        return $users;
    }

    public function createUser(User $user): bool
    {
        $pdo = Database::getInstance();

        $req = $pdo->prepare("INSERT INTO users(email, displayName, role, password) VALUES(:email, :displayName, :role, :password)");
        $req->bindParam(":email", $user->getEmail());
        $req->bindParam(":displayName", $user->getDisplayName());
        $req->bindParam(":role", $user->getRole());
        $req->bindParam(":password", $user->getPassword());

        return $req->execute();
    }

    public function deleteByEmail(string $email): void
    {
        $pdo = Database::getInstance();
        $req = $pdo->prepare("DELETE FROM users WHERE email LIKE :email");
        $req->bindParam(":email", $email);
        $req->execute();
    }

    public function promoteByEmail(string $email): void
    {
        $pdo = Database::getInstance();
        $req = $pdo->prepare("UPDATE users SET role='admin' WHERE email LIKE :email");
        $req->bindParam(":email", $email);
        $req->execute();
    }

    public function demoteByEmail(string $email): void
    {
        $pdo = Database::getInstance();
        $req = $pdo->prepare("UPDATE users SET role='visitor' WHERE email LIKE :email");
        $req->bindParam(":email", $email);
        $req->execute();
    }
}