<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function epics()
    {
        return $this->hasMany(Epic::class);
    }

    // Helper methods
    public function isOwner(User $user)
    {
        return $this->owner_id === $user->id;
    }

    public function isMember(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function hasAccess(User $user)
    {
        return $this->isOwner($user) || $this->isMember($user);
    }
}
