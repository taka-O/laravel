<?php

namespace App\Enums;

enum Role: int
{
    case admin = 1;
    case instructor = 2;
    case student = 3;

    public static function getByName(string $name): static
    {
        return match($name) {
            'admin' => static::admin,
            'instructor' => static::instructor,
            'student' => static::student,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::admin => '管理者',
            self::instructor => '講師',
            self::student => '学生',
        };
    }
}
