<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'whatsapp_number',
        'otp',
        'expires_at',
        'verified'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * Scope for valid OTPs (not expired and not verified)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', Carbon::now())
                    ->where('verified', false);
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified()
    {
        $this->update(['verified' => true]);
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
