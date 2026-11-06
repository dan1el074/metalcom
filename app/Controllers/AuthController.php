<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Content;

final class AuthController extends Controller
{
    public function form(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
        }
        $settings = Content::settings();
        $this->view('auth/login', [
            'title' => 'Acesso administrativo | Metalcom',
            'loginImage' => $settings['login_background_image'] ?? 'assets/soldador-metalcom-sem-filtro.png',
        ], 'auth');
    }

    public function login(): void
    {
        $this->requireCsrf();
        if (Auth::attempt((string) ($_POST['email'] ?? ''), (string) ($_POST['password'] ?? ''))) {
            $this->redirect('/admin');
        }
        flash('error', 'E-mail ou senha inválidos.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        $this->redirect('/login');
    }
}
