<?php

namespace App\Services;

use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentNumberGenerator
{
    /**
     * Generate the next sequential document number, e.g. "INV-2026-000086".
     *
     * The sequence is derived from the maximum existing numeric suffix in the
     * number column for the current year — never from COUNT(*) or MAX(id) —
     * so deleted or imported rows can never produce an already-used number.
     */
    public static function next(string $model, string $column, string $prefix, int $pad = 6): string
    {
        $yearPrefix = $prefix . '-' . now()->format('Y') . '-';

        $query = $model::query()->where($column, 'like', $yearPrefix . '%');
        if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            $query->withTrashed();
        }

        // Fixed-width zero-padded suffixes make lexicographic max equal to numeric max.
        $last = $query->lockForUpdate()->max($column);
        $seq = $last ? ((int) substr($last, -$pad)) + 1 : 1;

        return $yearPrefix . str_pad((string) $seq, $pad, '0', STR_PAD_LEFT);
    }
}
