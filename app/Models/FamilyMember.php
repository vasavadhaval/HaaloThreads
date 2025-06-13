<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FamilyMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'father_name',
        'surname',
        'age',
        'education',
        'blood_group',
        'occupation',
        'member_type',
        'marital_status',
        'profile_image', // Store only the filename
        'user_id', // Foreign key for the relationship with User
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
    ];

    /**
     * Get the user that this family member belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
}
