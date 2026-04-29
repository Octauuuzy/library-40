<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ['log_id', 'user_id', 'username', 'deskripsi'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            $latestLog = self::orderBy('id', 'desc')->first();
            if (!$latestLog) {
                $log->log_id = 'E000001';
            } else {
                $number = (int) substr($latestLog->log_id, 1);
                $log->log_id = 'E' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}