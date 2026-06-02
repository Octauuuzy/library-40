<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    use HasFactory;

    public const STATUS_DIPINJAM = 'dipinjam';
    public const STATUS_DIKEMBALIKAN = 'dikembalikan';
    public const STATUS_TERLAMBAT = 'terlambat';

    protected $table = 'peminjaman';

    protected $fillable = [
        'anggota_id',
        'buku_id',
        'tgl_pinjam',
        'tgl_kembali_rencana',
        'tgl_kembali_aktual',
        'status',
        'denda'
    ];

    protected $casts = [
        'tgl_pinjam' => 'date',
        'tgl_kembali_rencana' => 'date',
        'tgl_kembali_aktual' => 'date',
    ];

    public function setStatusAttribute(?string $value): void
    {
        $this->attributes['status'] = static::normalizeStatus($value);
    }

    public function getStatusNormalizedAttribute(): ?string
    {
        return static::normalizeStatus($this->attributes['status'] ?? null);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_normalized) {
            self::STATUS_DIPINJAM => 'Dipinjam',
            self::STATUS_DIKEMBALIKAN => 'Dikembalikan',
            self::STATUS_TERLAMBAT => 'Terlambat',
            default => Str::title((string) $this->status_normalized),
        };
    }

    public static function normalizeStatus(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        return Str::lower(trim($status));
    }

    public static function activeStatuses(): array
    {
        return [
            self::STATUS_DIPINJAM,
            self::STATUS_TERLAMBAT,
        ];
    }

    public function isBorrowed(): bool
    {
        return $this->status_normalized === self::STATUS_DIPINJAM;
    }

    public function isReturned(): bool
    {
        return $this->status_normalized === self::STATUS_DIKEMBALIKAN;
    }

    public function isLate(): bool
    {
        return $this->status_normalized === self::STATUS_TERLAMBAT;
    }

    public function isActive(): bool
    {
        return in_array($this->status_normalized, self::activeStatuses(), true);
    }

    public function scopeStatusIn(Builder $query, array|string $statuses): Builder
    {
        $normalizedStatuses = collect((array) $statuses)
            ->map(fn ($status) => static::normalizeStatus($status))
            ->filter()
            ->values()
            ->all();

        if ($normalizedStatuses === []) {
            return $query;
        }

        return $query->whereIn(DB::raw('LOWER(status)'), $normalizedStatuses);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->statusIn(self::activeStatuses());
    }

    public function scopeReturned(Builder $query): Builder
    {
        return $query->statusIn(self::STATUS_DIKEMBALIKAN);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
