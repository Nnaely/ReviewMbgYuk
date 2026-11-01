<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    public const STATUS_BARU = 'baru';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'reporter_id',
        'school_id',
        'distribution_id',
        'description',
        'photo_path',
        'status',
        'follow_up_note',
        'needs_referral',
        'referral_target',
        'referral_note',
        'referred_at',
        'referred_by',
    ];

    protected function casts(): array
    {
        return [
            'needs_referral' => 'boolean',
            'referred_at' => 'datetime',
        ];
    }

    public static function nextStatuses(string $status): array
    {
        return match ($status) {
            self::STATUS_BARU => [self::STATUS_DIPROSES],
            self::STATUS_DIPROSES => [self::STATUS_SELESAI],
            default => [],
        };
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class)->oldest();
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
}
