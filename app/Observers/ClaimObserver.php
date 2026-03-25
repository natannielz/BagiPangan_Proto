<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Claim;
use Illuminate\Support\Facades\Auth;

class ClaimObserver
{
    private function log(string $action, Claim $model, ?array $extra = null): void
    {
        AuditLog::create([
            'user_id'      => Auth::id(),
            'action'       => "claim.{$action}",
            'subject_type' => Claim::class,
            'subject_id'   => $model->id,
            'payload'      => array_merge([
                'donation_id' => $model->donation_id,
                'claimer_id'  => $model->claimer_id,
                'status'      => $model->status,
            ], $extra ?? []),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function created(Claim $model): void
    {
        $this->log('created', $model);
    }

    public function updated(Claim $model): void
    {
        $dirty = $model->getChanges();

        if (isset($dirty['status'])) {
            $this->log($dirty['status'], $model, ['old_status' => $model->getOriginal('status')]);
        }

        if (isset($dirty['proof_photo_path'])) {
            $this->log('proof_uploaded', $model);
        }
    }
}
