<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;
    protected $appends = ['single_image'];

    public function getSingleImageAttribute()
    {
        if ($this->fileAttachSingle) {
            return $this->fileAttachSingle;
        }
        return asset('assets/images/no-image.jpg');
    }

    public function fileAttachSingle()
    {
        return $this->hasOne(FileManager::class, 'id', 'file_id');
    }
}
