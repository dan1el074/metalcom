<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class InstallController extends Controller
{
    private function installed(): bool
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
    }

    public function form(): void
    {
        if ($this->installed()) {
            $this->redirect('/login');
        }
        $this->view('auth/install', ['title' => 'Configuração inicial | Metalcom'], 'auth');
    }

    public function create(): void
    {
        $this->requireCsrf();
        if ($this->installed()) {
            $this->redirect('/login');
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10) {
            flash('error', 'Informe nome, e-mail válido e uma senha com pelo menos 10 caracteres.');
            $this->redirect('/install');
        }
        $statement = Database::connection()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, "admin")');
        $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash('success', 'Administrador criado. Faça login para continuar.');
        $this->redirect('/login');
    }
}

