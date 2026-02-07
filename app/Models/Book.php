<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $connection = 'pgsql_app';

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'file_path',
        'file_size',
        'thumbnail_path',
        'uploaded_by',
        'category',
        'published_year',
        'is_active',
        'download_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'published_year' => 'integer',
    ];

    /**
     * Get the user who uploaded this book.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Increment download count.
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
