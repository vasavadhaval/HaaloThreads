<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'surname',
        'father_name',
        'email',
        'whatsapp_number',
        'password',
        'address',
        'blood_group',
        'education',
        'occupation',
        'age',
        'marital_status',
        'profile_image'

    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['profile_image_url'];

    public function getProfileImageUrlAttribute()
    {
        if (empty($this->profile_image)) {
            return null;
        }

        // For local storage
        if (config('filesystems.default') === 'public') {
            $filePath = 'profile_images/'.$this->profile_image;

            // Verify file exists
            if (!Storage::disk('public')->exists('profile_images/'.$this->profile_image)) {
                return null;
            }

            return 'https://73b6-103-251-59-209.ngrok-free.app'.asset('storage/'.$filePath);
        }

        // For cloud storage
        return 'https://73b6-103-251-59-209.ngrok-free.app'.Storage::url('profile_images/'.$this->profile_image);
    }
        // app/Models/User.php
    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }
}
