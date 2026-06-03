<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPeminjaman extends Model
{
    use HasFactory;

    public const JENIS_PINJAM = 'pinjam';
    public const JENIS_KEMBALIKAN = 'kembalikan';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'permintaan_peminjaman';

    protected $fillable = [
        'user_id',
        'anggota_id',
        'buku_id',
        'peminjaman_id',
        'jenis',
        'durasi',
        'status',
        'processed_by',
        'processed_at',
        'catatan_admin',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isBorrowRequest(): bool
    {
        return $this->jenis === self::JENIS_PINJAM;
    }

    public function isReturnRequest(): bool
    {
        return $this->jenis === self::JENIS_KEMBALIKAN;
    }

    public function getJenisLabelAttribute(): string
    {
        return $this->isBorrowRequest() ? 'Pinjam' : 'Kembalikan';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst((string) $this->status),
        };
    }

    public function getDurasiLabelAttribute(): string
    {
        if (!$this->durasi) {
            return '-';
        }

        return $this->durasi . ' hari';
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
