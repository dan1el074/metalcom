<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Content
{
    public static function settings(): array
    {
        $rows = Database::connection()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        return array_column($rows, 'setting_value', 'setting_key');
    }

    public static function categories(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM categories' . ($activeOnly ? ' WHERE active = 1' : '') . ' ORDER BY sort_order, id';
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function projects(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM special_projects' . ($activeOnly ? ' WHERE active = 1' : '') . ' ORDER BY sort_order, id';
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function products(?string $category = null, ?string $search = null): array
    {
        $sql = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.active = 1';
        $parameters = [];
        if ($category) {
            $sql .= ' AND c.slug = ?';
            $parameters[] = $category;
        }
        if ($search) {
            $sql .= ' AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)';
            $term = '%' . $search . '%';
            array_push($parameters, $term, $term, $term);
        }
        $sql .= ' ORDER BY p.sort_order, p.name';
        $statement = Database::connection()->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public static function product(string $slug): ?array
    {
        $statement = Database::connection()->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.slug = ? AND p.active = 1 LIMIT 1');
        $statement->execute([$slug]);
        $product = $statement->fetch();
        return $product ?: null;
    }

    public static function recordProductView(int $productId): void
    {
        Database::connection()->prepare('UPDATE products SET view_count = view_count + 1, updated_at = updated_at WHERE id = ? AND active = 1')->execute([$productId]);
    }

    public static function productImages(int $productId): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id');
        $statement->execute([$productId]);
        return $statement->fetchAll();
    }

    public static function showcaseImages(int $limit = 8): array
    {
        $limit = max(1, min($limit, 20));
        return Database::connection()->query('SELECT pi.*, p.name AS product_name, p.slug AS product_slug, c.name AS category_name FROM product_images pi JOIN products p ON p.id = pi.product_id JOIN categories c ON c.id = p.category_id WHERE pi.featured = 1 AND p.active = 1 ORDER BY pi.sort_order, pi.id LIMIT ' . $limit)->fetchAll();
    }

    public static function saveSettings(array $settings): void
    {
        $statement = Database::connection()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ($settings as $key => $value) {
            $statement->execute([$key, $value]);
        }
    }
}

