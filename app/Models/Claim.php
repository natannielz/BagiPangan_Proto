<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    protected $fillable = [
        'donation_id',
        'claimer_id',
        'claimed_at',
        'proof_photo_path',
        'proof_uploaded_at',
        'verified_at',
        'verifier_id',
        'status',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'proof_uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function claimer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimer_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}

