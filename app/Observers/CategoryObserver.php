<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Category;

class CategoryObserver
{
    public function created(Category $category): void
    {
        $this->log('category.created', $category, [
            'name' => $category->name,
            'slug' => $category->slug,
            'is_active' => (bool) $category->is_active,
        ]);
    }

    public function updated(Category $category): void
    {
        $changes = $category->getChanges();

        $this->log('category.updated', $category, [
            'changes' => $changes,
        ]);
    }

    public function deleted(Category $category): void
    {
        $this->log('category.deleted', $category, [
            'name' => $category->name,
            'slug' => $category->slug,
        ]);
    }

    protected function log(string $action, Category $category, array $payload): void
    {
        $request = request();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $category::class,
            'subject_id' => $category->id,
            'payload' => $payload,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}

