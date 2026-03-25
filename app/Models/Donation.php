<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donation extends Model
{
    protected $fillable = [
        'donor_id',
        'category_id',
        'title',
        'description',
        'qty_portions',
        'location_district',
        'expiry_at',
        'photo_path',
        'status',
        'moderation_status',
        'rejection_reason',
    ];

    protected $casts = [
        'expiry_at' => 'datetime',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
}
