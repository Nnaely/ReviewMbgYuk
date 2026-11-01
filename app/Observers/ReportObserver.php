<?php

namespace App\Observers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportObserver
{
    public function created(Report $report): void
    {
        $report->statusHistories()->create([
            'updated_by' => Auth::id() ?? $report->reporter_id,
            'status' => $report->status,
            'note' => 'Laporan dibuat oleh pelapor.',
        ]);
    }

    public function updated(Report $report): void
    {
        $trackedFields = [
            'status', 'follow_up_note', 'needs_referral', 'referral_target',
            'referral_note', 'referred_at', 'description', 'school_id',
            'distribution_id', 'photo_path',
        ];
        $changes = array_intersect_key($report->getChanges(), array_flip($trackedFields));

        if ($changes === []) {
            return;
        }

        $previous = $report->getPrevious();
        $referralChanged = array_key_exists('referral_target', $changes)
            || array_key_exists('referral_note', $changes)
            || array_key_exists('referred_at', $changes);

        $report->statusHistories()->create([
            'updated_by' => Auth::id() ?? $report->reporter_id,
            'previous_status' => $previous['status'] ?? $report->status,
            'status' => $report->status,
            'note' => $referralChanged
                ? ($report->referral_note ?: 'Penerusan laporan dicatat.')
                : ($report->follow_up_note ?: 'Data laporan diperbarui.'),
        ]);
    }
}
