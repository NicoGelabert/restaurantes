<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeHeroBanner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'image',
        'image_mime',
        'image_size',
        'headline',
        'description',
        'link',
        'other_info',
        'published',
        'created_by',
        'updated_by'];
}
