<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'attachable_type',
        'attachable_id',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent attachable model (Task, Comment, etc.)
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this attachment
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Get human readable file size
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute(): bool
    {
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        return in_array($this->mime_type, $imageTypes);
    }

    /**
     * Get the icon class for this file type
     */
    public function getIconAttribute(): string
    {
        if ($this->is_image) {
            return 'bi-image';
        }

        $icons = [
            'application/pdf' => 'bi-file-earmark-pdf',
            'application/msword' => 'bi-file-earmark-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'bi-file-earmark-word',
            'application/vnd.ms-excel' => 'bi-file-earmark-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'bi-file-earmark-excel',
            'application/zip' => 'bi-file-earmark-zip',
            'text/plain' => 'bi-file-earmark-text',
        ];

        return $icons[$this->mime_type] ?? 'bi-file-earmark';
    }

    /**
     * Get the color class for this file type
     */
    public function getColorAttribute(): string
    {
        if ($this->is_image) {
            return 'success';
        }

        $colors = [
            'application/pdf' => 'danger',
            'application/msword' => 'primary',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'primary',
            'application/vnd.ms-excel' => 'success',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'success',
            'application/zip' => 'warning',
            'text/plain' => 'secondary',
        ];

        return $colors[$this->mime_type] ?? 'secondary';
    }

    /**
     * Get the full URL for this attachment
     */
    public function getUrlAttribute(): string
    {
        return route('attachments.download', $this->id);
    }

    /**
     * Get the thumbnail URL for images
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->is_image) {
            return null;
        }

        return route('attachments.thumbnail', $this->id);
    }

    /**
     * Scopes
     */
    public function scopeForAttachable($query, $attachableType, $attachableId)
    {
        return $query->where('attachable_type', $attachableType)
                    ->where('attachable_id', $attachableId);
    }

    public function scopeByUploader($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    public function scopeImages($query)
    {
        return $query->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);
    }

    /**
     * Boot method to handle file deletion
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }
        });
    }
}
