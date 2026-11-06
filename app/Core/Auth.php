<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        static $user;
        if ($user === null) {
            $statement = Database::connection()->prepare('SELECT id, name, email, role, active FROM users WHERE id = ? LIMIT 1');
            $statement->execute([$_SESSION['user_id']]);
            $user = $statement->fetch() ?: false;
        }
        return $user ?: null;
    }

    public static function attempt(string $email, string $password): bool
    {
        $statement = Database::connection()->prepare('SELECT * FROM users WHERE email = ? AND active = 1 LIMIT 1');
        $statement->execute([mb_strtolower(trim($email))]);
        $user = $statement->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        Database::connection()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
        return true;
    }

    public static function requireLogin(): void
    {
        if (!self::check() || !self::user() || !(int) self::user()['active']) {
            unset($_SESSION['user_id']);
            flash('error', 'Faça login para acessar o painel.');
            header('Location: ' . url('/login'));
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if ((self::user()['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Apenas administradores podem executar esta ação.');
        }
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
    }
}

