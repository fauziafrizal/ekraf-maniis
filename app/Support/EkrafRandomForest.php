<?php

namespace App\Support;

class EkrafRandomForest
{
    public const FEATURES = ['marketing', 'branding', 'finance', 'legal', 'production'];

    public const LABELS = ['pemasaran', 'merek', 'keuangan', 'legalitas', 'produksi'];

    public function load(): ?array
    {
        $path = storage_path('app/private/ekraf-rf-model.json');
        if (! is_file($path) || filesize($path) > 10000000) {
            return null;
        }
        try {
            $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }
        if (($data['version'] ?? null) !== 1 || ($data['features'] ?? null) !== self::FEATURES || ! is_array($data['classes'] ?? null) || ! is_array($data['trees'] ?? null) || count($data['trees']) < 1 || count($data['trees']) > 200) {
            return null;
        }
        if (count($data['classes']) !== 5 || array_diff($data['classes'], self::LABELS) || count(array_unique($data['classes'])) !== 5) {
            return null;
        }

        return $data;
    }

    public function predict(array $model, array $features): ?array
    {
        $classes = $model['classes'];
        $scores = array_fill(0, count($classes), 0.0);
        foreach ($model['trees'] as $tree) {
            $node = 0;
            $leaf = false;
            for ($depth = 0; $depth < 64; $depth++) {
                if (! isset($tree['left'][$node],$tree['right'][$node],$tree['feature'][$node],$tree['threshold'][$node],$tree['value'][$node])) {
                    return null;
                }
                if ($tree['left'][$node] === -1 && $tree['right'][$node] === -1) {
                    $values = $tree['value'][$node];
                    if (! is_array($values) || count($values) !== count($classes)) {
                        return null;
                    }
                    foreach ($values as $value) {
                        if (! is_numeric($value) || $value < 0 || ! is_finite((float) $value)) {
                            return null;
                        }
                    }
                    $total = array_sum($values);
                    if ($total <= 0) {
                        return null;
                    }
                    foreach ($values as $index => $value) {
                        $scores[$index] += $value / $total;
                    }
                    $leaf = true;
                    break;
                }
                $feature = $tree['feature'][$node];
                if (! isset($features[$feature])) {
                    return null;
                }
                $node = $features[$feature] <= $tree['threshold'][$node] ? $tree['left'][$node] : $tree['right'][$node];
                if (! is_int($node) || $node < 0) {
                    return null;
                }
            }
            if (! $leaf) {
                return null;
            }
        }
        $scores = array_map(fn ($s) => $s / count($model['trees']), $scores);
        $best = array_search(max($scores), $scores, true);

        return ['label' => $classes[$best], 'score' => $scores[$best]];
    }
}
