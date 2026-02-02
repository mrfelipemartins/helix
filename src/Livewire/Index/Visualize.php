<?php

namespace MrFelipeMartins\Helix\Livewire\Index;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;

class Visualize extends Component
{
    public Index $index;

    /**
     * @return array<int, array{id: string, x: float, y: float, vector: array<int,float>, metadata?: mixed}>
     */
    #[Computed]
    public function points(): array
    {
        $paginator = Helix::list($this->index, page: 1, perPage: 200);
        $items = $paginator->items();

        if ($paginator->total() === 0 || count($items) === 0) {
            return [];
        }

        /** @var array<int, array{id: string, vector: array<int, float>, metadata?: mixed}> $points */
        $points = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $vector = $item['vector'] ?? null;
            $id = $item['id'] ?? null;

            if (! is_string($id) || ! is_array($vector)) {
                continue;
            }

            $points[] = [
                'id' => $id,
                'vector' => array_map('floatval', $vector),
                'metadata' => $item['metadata'] ?? null,
            ];
        }

        if ($points === []) {
            return [];
        }

        $minX = $maxX = (float) ($points[0]['vector'][0] ?? 0);
        $minY = $maxY = (float) ($points[0]['vector'][1] ?? 0);

        foreach ($points as $item) {
            $x = (float) ($item['vector'][0] ?? 0);
            $y = (float) ($item['vector'][1] ?? 0);

            $minX = min($minX, $x);
            $maxX = max($maxX, $x);
            $minY = min($minY, $y);
            $maxY = max($maxY, $y);
        }

        $normalize = function (float $value, float $min, float $max): float {
            if ($max - $min == 0.0) {
                return 0.5;
            }

            return (float) (($value - $min) / ($max - $min));
        };

        $normalized = [];
        foreach ($points as $item) {
            $x = (float) ($item['vector'][0] ?? 0);
            $y = (float) ($item['vector'][1] ?? 0);

            $normalized[] = [
                'id' => $item['id'],
                'x' => $normalize($x, $minX, $maxX),
                'y' => $normalize($y, $minY, $maxY),
                'vector' => $item['vector'],
                'metadata' => $item['metadata'] ?? null,
            ];
        }

        return $normalized;
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.tabs');
    }

    public function render(): View
    {
        return view('helix::index.visualize');
    }
}
