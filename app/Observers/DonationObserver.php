<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DonationObserver
{
    private function log(string $action, Donation $model, ?array $changes = null): void
    {
        AuditLog::create([
            'user_id'      => Auth::id(),
            'action'       => "donation.{$action}",
            'subject_type' => Donation::class,
            'subject_id'   => $model->id,
            'payload'      => [
                'title'   => $model->title,
                'status'  => $model->status,
                'changes' => $changes,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function flushCache(): void
    {
        try {
            Cache::tags(['donations'])->flush();
        } catch (\Throwable) {
            // Cache driver may not support tags (e.g. file/array in tests)
        }
    }

    public function created(Donation $model): void
    {
        $this->log('created', $model);
        $this->flushCache();
    }

    public function updated(Donation $model): void
    {
        $dirty = $model->getChanges();
        if (empty($dirty)) {
            return;
        }
        $this->log('updated', $model, ['old' => $model->getOriginal(), 'new' => $dirty]);
        $this->flushCache();
    }

    public function deleted(Donation $model): void
    {
        $this->log('deleted', $model);
        $this->flushCache();
    }
}
