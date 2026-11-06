<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Content;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home', [
            'title' => 'Metalcom | Soluções em metal para todos os projetos',
            'settings' => Content::settings(),
            'categories' => Content::categories(),
            'projects' => Content::projects(),
        ]);
    }

    public function contact(): void
    {
        $this->requireCsrf();
        if (!empty($_POST['company'])) {
            $this->redirect('/');
        }
        if (isset($_SESSION['last_contact_at']) && time() - (int) $_SESSION['last_contact_at'] < 10) {
            flash('error', 'Aguarde alguns segundos antes de enviar novamente.');
            $this->redirect('/#contato');
        }
        $data = [
            'name' => mb_substr(trim((string) ($_POST['name'] ?? '')), 0, 120),
            'phone' => mb_substr(trim((string) ($_POST['phone'] ?? '')), 0, 40),
            'email' => filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL) ?: '',
            'subject' => mb_substr(str_replace(["\r", "\n"], ' ', trim((string) ($_POST['subject'] ?? ''))), 0, 190),
            'message' => mb_substr(trim((string) ($_POST['message'] ?? '')), 0, 5000),
        ];
        if ($data['name'] === '' || $data['phone'] === '' || $data['email'] === '' || $data['subject'] === '') {
            flash('error', 'Preencha os campos obrigatórios corretamente.');
            $_SESSION['old'] = $_POST;
            $this->redirect('/#contato');
        }

        $statement = Database::connection()->prepare('INSERT INTO contact_submissions (name, phone, email, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([$data['name'], $data['phone'], $data['email'], $data['subject'], $data['message'], $_SERVER['REMOTE_ADDR'] ?? null, substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)]);
        $submissionId = (int) Database::connection()->lastInsertId();
        $_SESSION['last_contact_at'] = time();

        $settings = Content::settings();
        $destination = $settings['contact_email'] ?? 'contato@metalcom.com.br';
        $body = "Novo contato pelo site\n\nNome: {$data['name']}\nTelefone: {$data['phone']}\nE-mail: {$data['email']}\nAssunto: {$data['subject']}\n\n{$data['message']}";
        $headers = 'From: ' . config('mail.from_name') . ' <' . config('mail.from_address') . ">\r\nReply-To: {$data['email']}";

        $action = ($settings['contact_action'] ?? 'whatsapp') === 'email' ? 'email' : 'whatsapp';
        $metalcomEmailSent = null;
        $customerEmailSent = null;
        if ($action === 'email') {
            $metalcomEmailSent = @mail($destination, 'Contato pelo site: ' . $data['subject'], $body, $headers);
        }
        if (($settings['customer_auto_reply'] ?? '0') === '1') {
            $reply = $settings['customer_reply_text'] ?? 'Recebemos sua mensagem e retornaremos em breve.';
            $customerEmailSent = @mail($data['email'], 'Recebemos sua mensagem | Metalcom', $reply, 'From: ' . config('mail.from_name') . ' <' . config('mail.from_address') . '>');
        }
        Database::connection()->prepare('UPDATE contact_submissions SET delivery_method = ?, metalcom_email_sent = ?, customer_email_sent = ? WHERE id = ?')->execute([
            $action,
            $metalcomEmailSent === null ? null : (int) $metalcomEmailSent,
            $customerEmailSent === null ? null : (int) $customerEmailSent,
            $submissionId,
        ]);

        if ($action === 'whatsapp') {
            $number = preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '5554991634643');
            $text = "Olá, sou {$data['name']}. {$data['subject']}. {$data['message']}";
            header('Location: https://wa.me/' . $number . '?text=' . rawurlencode($text));
            exit;
        }

        flash('success', 'Mensagem recebida. A equipe Metalcom entrará em contato.');
        $this->redirect('/#contato');
    }
}

