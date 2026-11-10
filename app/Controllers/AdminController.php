<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Upload;
use App\Models\Content;
use PDO;
use Throwable;

final class AdminController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $db = Database::connection();
        $stats = [
            'products' => (int) $db->query('SELECT COUNT(*) FROM products WHERE active = 1')->fetchColumn(),
            'categories' => (int) $db->query('SELECT COUNT(*) FROM categories WHERE active = 1')->fetchColumn(),
            'messages' => (int) $db->query('SELECT COUNT(*) FROM contact_submissions')->fetchColumn(),
            'unread' => (int) $db->query('SELECT COUNT(*) FROM contact_submissions WHERE read_at IS NULL')->fetchColumn(),
        ];
        $products = $db->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY c.name ASC, p.sort_order ASC, p.name ASC, p.id ASC')->fetchAll();
        $galleryRows = $db->query('SELECT id, product_id, image_path, alt_text, sort_order, featured FROM product_images ORDER BY product_id, sort_order, id')->fetchAll();
        $galleryByProduct = [];
        foreach ($galleryRows as $galleryRow) {
            $galleryRow['image_url'] = asset($galleryRow['image_path']);
            $galleryByProduct[(int) $galleryRow['product_id']][] = $galleryRow;
        }
        foreach ($products as &$product) {
            $product['image_url'] = asset($product['image_path']);
            $product['gallery_images'] = $galleryByProduct[(int) $product['id']] ?? [];
        }
        unset($product);
        $this->view('admin/dashboard', [
            'title' => 'Painel administrativo | Metalcom',
            'user' => Auth::user(),
            'settings' => Content::settings(),
            'categories' => $db->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY c.sort_order, c.id')->fetchAll(),
            'projects' => Content::projects(false),
            'products' => $products,
            'users' => $db->query('SELECT id, name, email, role, active, last_login_at, created_at FROM users ORDER BY name')->fetchAll(),
            'messages' => $db->query('SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 100')->fetchAll(),
            'stats' => $stats,
        ], 'admin');
    }

    public function saveCategory(): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $db = Database::connection();
        try {
            if ($id < 0 || $name === '' || mb_strlen($name) > 100) {
                throw new \RuntimeException('Informe um nome de categoria com até 100 caracteres.');
            }
            $duplicate = $db->prepare('SELECT COUNT(*) FROM categories WHERE name = ? AND id <> ?');
            $duplicate->execute([$name, $id]);
            if ((int) $duplicate->fetchColumn() > 0) {
                throw new \RuntimeException('Já existe uma categoria com esse nome.');
            }
            if ($id) {
                $existing = $db->prepare('SELECT id FROM categories WHERE id = ?');
                $existing->execute([$id]);
                if (!$existing->fetchColumn()) {
                    throw new \RuntimeException('Categoria não encontrada.');
                }
                $db->prepare('UPDATE categories SET name = ? WHERE id = ?')->execute([$name, $id]);
            } else {
                $slugBase = trim((string) preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($name)) ?: 'categoria'), '-') ?: 'categoria';
                $slug = $slugBase;
                $suffix = 2;
                $slugCheck = $db->prepare('SELECT COUNT(*) FROM categories WHERE slug = ?');
                $slugCheck->execute([$slug]);
                while ((int) $slugCheck->fetchColumn() > 0) {
                    $slug = $slugBase . '-' . $suffix++;
                    $slugCheck->execute([$slug]);
                }
                $order = (int) $db->query('SELECT COALESCE(MAX(sort_order), 0) FROM categories')->fetchColumn() + 10;
                $db->prepare('INSERT INTO categories (name, slug, description, image_path, sort_order, active) VALUES (?, ?, ?, ?, ?, 1)')
                    ->execute([$name, $slug, '', 'assets/categoria-sem-imagem.svg', $order]);
            }
            flash('success', $id ? 'Categoria atualizada.' : 'Categoria criada.');
        } catch (Throwable $exception) {
            flash('error', $exception instanceof \PDOException ? 'Não foi possível salvar a categoria. Tente novamente.' : $exception->getMessage());
        }
        $this->redirect('/admin#categorias-admin');
    }

    public function deleteCategory(int $id): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $db = Database::connection();
        try {
            $db->beginTransaction();
            $existing = $db->prepare('SELECT id FROM categories WHERE id = ? FOR UPDATE');
            $existing->execute([$id]);
            if (!$existing->fetchColumn()) {
                throw new \RuntimeException('Categoria não encontrada.');
            }
            $products = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
            $products->execute([$id]);
            if ((int) $products->fetchColumn() > 0) {
                throw new \RuntimeException('Esta categoria possui produtos vinculados. Altere a categoria desses produtos antes de excluir.');
            }
            $db->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            $db->commit();
            flash('success', 'Categoria excluída.');
        } catch (Throwable $exception) {
            if ($db->inTransaction()) $db->rollBack();
            flash('error', $exception instanceof \PDOException ? 'Não foi possível excluir a categoria. Verifique se existem produtos vinculados.' : $exception->getMessage());
        }
        $this->redirect('/admin#categorias-admin');
    }

    public function saveHome(): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $section = (string) ($_POST['home_section'] ?? '');
        $sectionNames = ['hero_text' => 'Textos do destaque principal', 'hero' => 'Imagens do hero', 'products' => 'Nossos produtos', 'about' => 'Sobre a Metalcom', 'projects' => 'Projetos especiais'];
        if (!isset($sectionNames[$section])) {
            flash('error', 'Selecione uma seção válida da página inicial.');
            $this->redirect('/admin#configuracoes-home');
        }
        $db = Database::connection();
        try {
            $db->beginTransaction();
            $currentSettings = Content::settings();
            $updatedSettings = [];
            if ($section === 'hero_text') {
                $updatedSettings['hero_title'] = trim((string) ($_POST['hero_title'] ?? ''));
                $updatedSettings['hero_description'] = trim((string) ($_POST['hero_description'] ?? ''));
                if ($updatedSettings['hero_title'] === '') {
                    throw new \RuntimeException('Informe o título do destaque principal.');
                }
            } elseif ($section === 'hero') {
                $heroFallbacks = [1 => 'assets/hero-reboque-cavalo.png', 2 => 'uploads/9c29e64ae3fe63a24e34c493891174f5.png', 3 => 'assets/hero-calefator-ambiente-v2.png'];
                foreach ($heroFallbacks as $index => $fallback) {
                    $field = 'hero_image_' . $index;
                    $updatedSettings[$field] = Upload::image($field, $currentSettings[$field] ?? $fallback);
                }
            } else {
                foreach (['eyebrow', 'title', 'description'] as $suffix) {
                    $field = $section . '_' . $suffix;
                    $updatedSettings[$field] = trim((string) ($_POST[$field] ?? $currentSettings[$field] ?? ''));
                }
                if (in_array($section, ['products', 'about'], true) && $updatedSettings[$section . '_title'] === '') {
                    throw new \RuntimeException('Informe o título da seção.');
                }
                if ($section === 'about') {
                    $updatedSettings['about_image'] = Upload::image('about_image', $currentSettings['about_image'] ?? 'assets/soldador-metalcom-sem-filtro.png');
                }
            }
            Content::saveSettings($updatedSettings);

            foreach (($section === 'products' ? ($_POST['categories'] ?? []) : []) as $id => $row) {
                $id = (int) $id;
                if (!empty($row['delete'])) {
                    $productCount = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
                    $productCount->execute([$id]);
                    if ((int) $productCount->fetchColumn() > 0) {
                        throw new \RuntimeException('Não é possível excluir uma categoria que ainda possui produtos. Mova ou exclua esses produtos primeiro.');
                    }
                    $db->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
                    continue;
                }
                $image = Upload::image('category_image_' . (int) $id, $row['current_image'] ?? null);
                $statement = $db->prepare('UPDATE categories SET name = ?, description = ?, image_path = ?, sort_order = ?, active = ? WHERE id = ?');
                $statement->execute([trim($row['name']), trim($row['description']), $image, (int) $row['sort_order'], isset($row['active']) ? 1 : 0, (int) $id]);
            }
            foreach (($section === 'products' ? ($_POST['new_categories'] ?? []) : []) as $key => $row) {
                $name = trim((string) ($row['name'] ?? ''));
                $image = Upload::image('new_category_image_' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $key));
                if ($name === '' || !$image) {
                    throw new \RuntimeException('Informe o nome e a imagem de cada nova categoria.');
                }
                $slugBase = trim((string) preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($name)) ?: 'categoria'), '-');
                $slugBase = $slugBase ?: 'categoria';
                $slug = $slugBase;
                $suffix = 2;
                $slugCheck = $db->prepare('SELECT COUNT(*) FROM categories WHERE slug = ?');
                $slugCheck->execute([$slug]);
                while ((int) $slugCheck->fetchColumn() > 0) {
                    $slug = $slugBase . '-' . $suffix++;
                    $slugCheck->execute([$slug]);
                }
                $statement = $db->prepare('INSERT INTO categories (name, slug, description, image_path, sort_order, active) VALUES (?, ?, ?, ?, ?, ?)');
                $statement->execute([$name, $slug, trim((string) ($row['description'] ?? '')), $image, (int) ($row['sort_order'] ?? 0), isset($row['active']) ? 1 : 0]);
            }
            foreach (($section === 'projects' ? ($_POST['projects'] ?? []) : []) as $id => $row) {
                $id = (int) $id;
                if (!empty($row['delete'])) {
                    $db->prepare('DELETE FROM special_projects WHERE id = ?')->execute([$id]);
                    continue;
                }
                $image = Upload::image('project_image_' . (int) $id, $row['current_image'] ?? null);
                $statement = $db->prepare('UPDATE special_projects SET title = ?, image_path = ?, alt_text = ?, sort_order = ?, active = ? WHERE id = ?');
                $statement->execute([trim($row['title']), $image, trim($row['alt_text']), (int) $row['sort_order'], isset($row['active']) ? 1 : 0, (int) $id]);
            }
            foreach (($section === 'projects' ? ($_POST['new_projects'] ?? []) : []) as $key => $row) {
                $title = trim((string) ($row['title'] ?? ''));
                $image = Upload::image('new_project_image_' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $key));
                if ($title === '' || !$image) {
                    throw new \RuntimeException('Informe o título e a imagem de cada novo projeto especial.');
                }
                $statement = $db->prepare('INSERT INTO special_projects (title, image_path, alt_text, sort_order, active) VALUES (?, ?, ?, ?, ?)');
                $statement->execute([$title, $image, trim((string) ($row['alt_text'] ?? '')), (int) ($row['sort_order'] ?? 0), isset($row['active']) ? 1 : 0]);
            }
            $db->commit();
            flash('success', $sectionNames[$section] . ': alterações salvas.');
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            flash('error', $exception->getMessage());
        }
        $this->redirect('/admin#configuracoes-home');
    }

    public function saveContact(): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $action = in_array($_POST['contact_action'] ?? '', ['email', 'whatsapp'], true) ? $_POST['contact_action'] : 'whatsapp';
        Content::saveSettings([
            'contact_phone' => trim((string) ($_POST['contact_phone'] ?? '')),
            'contact_whatsapp' => preg_replace('/\D+/', '', (string) ($_POST['contact_whatsapp'] ?? '')),
            'contact_email' => filter_var($_POST['contact_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '',
            'contact_address' => trim((string) ($_POST['contact_address'] ?? '')),
            'contact_instagram' => trim((string) ($_POST['contact_instagram'] ?? '')),
            'contact_facebook' => trim((string) ($_POST['contact_facebook'] ?? '')),
            'contact_action' => $action,
            'customer_auto_reply' => isset($_POST['customer_auto_reply']) ? '1' : '0',
            'customer_reply_text' => trim((string) ($_POST['customer_reply_text'] ?? '')),
        ]);
        flash('success', 'Informações de contato atualizadas.');
        $this->redirect('/admin#contato-admin');
    }

    public function saveLoginAppearance(): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        try {
            $image = Upload::image('login_background_image', $_POST['current_login_background_image'] ?? 'assets/soldador-metalcom-sem-filtro.png');
            Content::saveSettings(['login_background_image' => $image]);
            flash('success', 'Imagem da tela de login atualizada.');
        } catch (Throwable $exception) {
            flash('error', $exception->getMessage());
        }
        $this->redirect('/admin#login-admin');
    }

    public function saveUser(): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();
        $db = Database::connection();
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $role = in_array($_POST['role'] ?? '', ['admin', 'editor'], true) ? $_POST['role'] : 'editor';
        $password = (string) ($_POST['password'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;

        if ($id === (int) Auth::user()['id'] && ($role !== 'admin' || !$active)) {
            flash('error', 'Você não pode remover seu próprio perfil de administrador nem desativar seu acesso.');
            $this->redirect('/admin#usuarios-admin');
        }

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || (!$id && strlen($password) < 8)) {
            flash('error', 'Informe nome, e-mail válido e senha com pelo menos 8 caracteres.');
            $this->redirect('/admin#usuarios-admin');
        }

        try {
            if ($id) {
                $statement = $db->prepare('UPDATE users SET name = ?, email = ?, role = ?, active = ? WHERE id = ?');
                $statement->execute([$name, $email, $role, $active, $id]);
                if ($password !== '') {
                    if (strlen($password) < 8) {
                        throw new \RuntimeException('A nova senha deve ter pelo menos 8 caracteres.');
                    }
                    $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
                }
            } else {
                $statement = $db->prepare('INSERT INTO users (name, email, password_hash, role, active) VALUES (?, ?, ?, ?, ?)');
                $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $active]);
            }
            flash('success', $id ? 'Usuário atualizado.' : 'Usuário criado.');
        } catch (Throwable $exception) {
            flash('error', str_contains($exception->getMessage(), 'Duplicate') ? 'Este e-mail já está em uso.' : $exception->getMessage());
        }
        $this->redirect('/admin#usuarios-admin');
    }

    public function toggleUser(int $id): void
    {
        Auth::requireAdmin();
        $this->requireCsrf();
        if ($id === (int) Auth::user()['id']) {
            flash('error', 'Você não pode desativar o próprio usuário.');
        } else {
            Database::connection()->prepare('UPDATE users SET active = NOT active WHERE id = ?')->execute([$id]);
            flash('success', 'Status do usuário alterado.');
        }
        $this->redirect('/admin#usuarios-admin');
    }

    public function readMessage(int $id): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        Database::connection()->prepare('UPDATE contact_submissions SET read_at = COALESCE(read_at, NOW()) WHERE id = ?')->execute([$id]);
        $this->redirect('/admin#mensagens-admin');
    }

    public function saveProduct(): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $slug = preg_replace('/[^a-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($slug ?: $name)) ?: 'produto');
        $slug = trim($slug, '-');
        if ($name === '' || $slug === '' || !(int) ($_POST['category_id'] ?? 0)) {
            flash('error', 'Preencha nome e categoria do produto.');
            $this->redirect('/admin#produtos-admin');
        }
        try {
            $image = Upload::image('image', $_POST['current_image'] ?? null);
            if (!$image) throw new \RuntimeException('Selecione a imagem principal do produto.');
            $values = [(int) $_POST['category_id'], $name, $slug, trim((string) ($_POST['description'] ?? '')), trim((string) ($_POST['code'] ?? '')), $image, (int) ($_POST['sort_order'] ?? 0), isset($_POST['active']) ? 1 : 0];
            if ($id) {
                $statement = Database::connection()->prepare('UPDATE products SET category_id=?, name=?, slug=?, description=?, code=?, image_path=?, sort_order=?, active=? WHERE id=?');
                $values[] = $id;
            } else {
                $statement = Database::connection()->prepare('INSERT INTO products (category_id,name,slug,description,code,image_path,sort_order,active) VALUES (?,?,?,?,?,?,?,?)');
            }
            $statement->execute($values);
            $productId = $id ?: (int) Database::connection()->lastInsertId();
            if ($id) {
                $existingGallery = $_POST['existing_gallery'] ?? [];
                $galleryUpdate = Database::connection()->prepare('UPDATE product_images SET sort_order = ? WHERE id = ? AND product_id = ?');
                $galleryDelete = Database::connection()->prepare('DELETE FROM product_images WHERE id = ? AND product_id = ?');
                foreach ($existingGallery as $imageId => $galleryItem) {
                    $imageId = (int) $imageId;
                    if ($imageId < 1) continue;
                    if (isset($galleryItem['delete'])) {
                        $galleryDelete->execute([$imageId, $productId]);
                    } else {
                        $galleryUpdate->execute([(int) ($galleryItem['sort_order'] ?? 0), $imageId, $productId]);
                    }
                }
            }
            $gallery = Upload::images('gallery_images');
            if ($gallery) {
                $nextOrder = (int) Database::connection()->query('SELECT COALESCE(MAX(sort_order), 0) FROM product_images WHERE product_id = ' . $productId)->fetchColumn();
                $galleryStatement = Database::connection()->prepare('INSERT INTO product_images (product_id, image_path, alt_text, sort_order) VALUES (?, ?, ?, ?)');
                foreach ($gallery as $path) {
                    $nextOrder += 10;
                    $galleryStatement->execute([$productId, $path, $name, $nextOrder]);
                }
            }
            flash('success', $id ? 'Produto atualizado.' : 'Produto cadastrado.');
        } catch (Throwable $exception) {
            flash('error', str_contains($exception->getMessage(), 'Duplicate') ? 'Já existe um produto com esse endereço.' : $exception->getMessage());
        }
        $this->redirect('/admin#produtos-admin');
    }

    public function deleteProduct(int $id): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $db = Database::connection();
        try {
            $db->beginTransaction();
            $statement = $db->prepare('SELECT id FROM products WHERE id = ? FOR UPDATE');
            $statement->execute([$id]);
            if (!$statement->fetchColumn()) {
                throw new \RuntimeException('Produto não encontrado.');
            }
            $db->prepare('DELETE FROM product_images WHERE product_id = ?')->execute([$id]);
            $db->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
            $db->commit();
            flash('success', 'Produto excluído.');
        } catch (Throwable $exception) {
            if ($db->inTransaction()) $db->rollBack();
            flash('error', 'Não foi possível excluir o produto. Tente novamente.');
        }
        $this->redirect('/admin#produtos-admin');
    }

    public function toggleProduct(int $id): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        Database::connection()->prepare('UPDATE products SET active = NOT active WHERE id = ?')->execute([$id]);
        flash('success', 'Status do produto alterado.');
        $this->redirect('/admin#produtos-admin');
    }
}

