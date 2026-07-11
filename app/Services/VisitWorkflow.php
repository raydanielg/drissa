<?php

namespace App\Services;

use App\Enums\VisitStatus;
use App\Exceptions\InvalidTransitionException;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class VisitWorkflow
{
    public function transition(Visit $visit, VisitStatus $to, ?string $note = null): Visit
    {
        $current = VisitStatus::from($visit->status);

        if (! in_array($to, $current->allowedTransitions())) {
            throw new InvalidTransitionException(
                "Cannot move visit from {$current->value} to {$to->value}"
            );
        }

        DB::transaction(function () use ($visit, $current, $to, $note) {
            $visit->update(['status' => $to->value]);

            $visit->statusLogs()->create([
                'from_status' => $current->value,
                'to_status' => $to->value,
                'changed_by' => auth()->id(),
                'note' => $note,
            ]);

            if ($to === VisitStatus::Completed) {
                $visit->update(['completed_at' => now()]);
            }
        });

        return $visit;
    }
}
