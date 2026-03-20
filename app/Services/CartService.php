<?php

namespace App\Services;

use App\Models\CameraListing;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class CartService
{
    public const SESSION_KEY = 'cart';

    public function get(): array
    {
        return array_values(Session::get(self::SESSION_KEY, []));
    }

    public function add(string $type, int $id, int $quantity = 1): void
    {
        $items = $this->keyByIdentity($this->get());
        $key = $this->key($type, $id);
        $existingQuantity = $items[$key]['quantity'] ?? 0;

        $items[$key] = [
            'type' => $type,
            'id' => $id,
            'quantity' => $this->normalizeQuantity($type, $id, $existingQuantity + $quantity),
        ];

        $this->persist($items);
    }

    public function remove(string $type, int $id): void
    {
        $items = $this->keyByIdentity($this->get());

        unset($items[$this->key($type, $id)]);

        $this->persist($items);
    }

    public function update(string $type, int $id, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($type, $id);

            return;
        }

        $items = $this->keyByIdentity($this->get());
        $key = $this->key($type, $id);

        if (! array_key_exists($key, $items)) {
            return;
        }

        $items[$key]['quantity'] = $this->normalizeQuantity($type, $id, $quantity);

        $this->persist($items);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function total(): int
    {
        return (int) collect($this->items())->sum('subtotal');
    }

    public function count(): int
    {
        return (int) collect($this->items())->sum('quantity');
    }

    public function items(): array
    {
        $rawItems = $this->get();

        if ($rawItems === []) {
            return [];
        }

        $products = Product::with('media')
            ->whereIn('id', $this->idsForType($rawItems, 'product'))
            ->get()
            ->keyBy('id');

        $cameras = CameraListing::with('media')
            ->whereIn('id', $this->idsForType($rawItems, 'camera'))
            ->get()
            ->keyBy('id');

        $validItems = [];
        $enrichedItems = [];

        foreach ($rawItems as $item) {
            $model = $item['type'] === 'product'
                ? $products->get($item['id'])
                : $cameras->get($item['id']);

            if (! $model || ! $this->isPurchasable($item['type'], $model)) {
                continue;
            }

            $quantity = $this->normalizeQuantity($item['type'], $model->id, $item['quantity'], $model);

            if ($quantity <= 0) {
                continue;
            }

            $validItems[] = [
                'type' => $item['type'],
                'id' => $model->id,
                'quantity' => $quantity,
            ];

            $enrichedItems[] = [
                'type' => $item['type'],
                'id' => $model->id,
                'quantity' => $quantity,
                'price' => $model->price,
                'subtotal' => $model->price * $quantity,
                'model' => $model,
            ];
        }

        if ($validItems !== $rawItems) {
            $this->persist($this->keyByIdentity($validItems));
        }

        return $enrichedItems;
    }

    protected function persist(array $items): void
    {
        Session::put(self::SESSION_KEY, array_values($items));
    }

    protected function key(string $type, int $id): string
    {
        return "{$type}:{$id}";
    }

    protected function keyByIdentity(array $items): array
    {
        return collect($items)
            ->mapWithKeys(fn (array $item): array => [$this->key($item['type'], $item['id']) => $item])
            ->all();
    }

    protected function idsForType(array $items, string $type): array
    {
        return collect($items)
            ->where('type', $type)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    protected function normalizeQuantity(string $type, int $id, int $quantity, Product | CameraListing | null $model = null): int
    {
        $model ??= $type === 'product'
            ? Product::query()->find($id)
            : CameraListing::query()->find($id);

        if (! $model || ! $this->isPurchasable($type, $model)) {
            return 0;
        }

        if ($type === 'camera') {
            return 1;
        }

        return max(1, min($quantity, (int) $model->stock_quantity));
    }

    protected function isPurchasable(string $type, Product | CameraListing $model): bool
    {
        return match ($type) {
            'product' => $model instanceof Product
                && $model->is_active
                && $model->stock_quantity > 0,
            'camera' => $model instanceof CameraListing
                && $model->status === 'available',
            default => throw new InvalidArgumentException('Unsupported cart item type.'),
        };
    }
}
