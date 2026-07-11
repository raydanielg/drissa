<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PatientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'uploaded_by', 'title', 'description', 'file_path', 'file_type', 'file_size', 'category',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function fileUrl(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function formattedSize(): string
    {
        $size = $this->file_size ?? 0;
        if ($size >= 1048576) return round($size / 1048576, 2) . ' MB';
        if ($size >= 1024) return round($size / 1024, 2) . ' KB';
        return $size . ' B';
    }

    protected static function booted(): void
    {
        static::deleting(function ($document) {
            Storage::disk('public')->delete($document->file_path);
        });
    }
}
