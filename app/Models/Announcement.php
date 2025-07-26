<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Announcement extends Model
{
    use HasFactory;

    public function announcementCourses(): HasMany
    {
        return $this->hasMany(AnnouncementCourse::class);
    }

    protected $visible = [
        'id',
        'title',
        'content',
        'start_at',
        'end_at',
    ];
}
