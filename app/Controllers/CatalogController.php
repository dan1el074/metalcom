<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Content;

final class CatalogController extends Controller
{
    public function index(): void
    {
        $category = trim((string) ($_GET['categoria'] ?? ''));
        $search = trim((string) ($_GET['busca'] ?? ''));
        $this->view('catalog/index', [
            'title' => 'Produtos | Metalcom',
            'products' => Content::products($category ?: null, $search ?: null),
            'categories' => Content::categories(),
            'selectedCategory' => $category,
            'search' => $search,
        ]);
    }

    public function show(): void
    {
        $product = Content::product(trim((string) ($_GET['produto'] ?? '')));
        if (!$product) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Produto não encontrado | Metalcom']);
            return;
        }
        Content::recordProductView((int) $product['id']);
        $sameCategory = array_values(array_filter(Content::products($product['category_slug']), fn ($item) => (int) $item['id'] !== (int) $product['id']));
        $allProducts = array_values(array_filter(Content::products(), fn ($item) => (int) $item['id'] !== (int) $product['id']));
        $relatedById = [];
        foreach (array_merge($sameCategory, $allProducts) as $item) {
            $relatedById[(int) $item['id']] = $item;
        }
        $related = array_values($relatedById);
        $images = Content::productImages((int) $product['id']);
        if (!$images) {
            $images[] = ['image_path' => $product['image_path'], 'alt_text' => $product['name']];
        }
        $this->view('catalog/show', ['title' => $product['name'] . ' | Metalcom', 'product' => $product, 'images' => $images, 'related' => array_slice($related, 0, 4)]);
    }
}

