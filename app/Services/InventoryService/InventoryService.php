<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;

class InventoryService
{
    public function recordSale(string $productId, string $productName, int $qty, string $orderNumber): void
    {
        Product::decrementStock($productId, $qty);
        Inventory::log($productId, $productName, STOCK_OUT, $qty, 'Order sale', $orderNumber);
    }

    public function restock(string $productId, int $qty, string $reason = 'Manual restock'): array
    {
        $product = Product::find($productId);
        if (!$product) return ['success' => false, 'message' => 'Product not found.'];

        Product::incrementStock($productId, $qty);
        Inventory::log($productId, $product['name'], STOCK_IN, $qty, $reason);

        return ['success' => true, 'product' => Product::find($productId)];
    }

    public function adjust(string $productId, int $newQuantity, string $reason = 'Stock adjustment'): array
    {
        $product = Product::find($productId);
        if (!$product) return ['success' => false, 'message' => 'Product not found.'];

        $diff = $newQuantity - (int) ($product['stock'] ?? 0);
        Product::update($productId, ['stock' => $newQuantity]);
        Inventory::log($productId, $product['name'], STOCK_ADJUSTMENT, $diff, $reason);

        return ['success' => true, 'product' => Product::find($productId)];
    }

    public function lowStockAlerts(): array
    {
        return Product::lowStock(LOW_STOCK_THRESHOLD);
    }

    public function movementHistory(?string $productId = null, int $limit = 50): array
    {
        return $productId ? Inventory::forProduct($productId, $limit) : Inventory::recent($limit);
    }
}
