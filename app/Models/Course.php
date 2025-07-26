<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    public function courseInstructors(): HasMany
    {
        return $this->hasMany(CourseUser::class)->where('user_type', 'instructor');
    }

    public function instructors(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, CourseUser::class, 'course_id', 'id', null, 'user_id')
                ->where('user_type', 'instructor');
    }

    public function courseStudents(): HasMany
    {
        return $this->hasMany(CourseUser::class)->where('user_type', 'student');
    }

    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, CourseUser::class, 'course_id', 'id', null, 'user_id')
                ->where('user_type', 'student');
    }
}
