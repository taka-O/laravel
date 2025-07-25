<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
use App\Enums\Role;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pid',
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'pid',
        'name',
        'email',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['role'];

    public function getRoleAttribute(): string
    {
        return Role::from($this->role_type)->name;
    }

    public function setRoleAttribute(Role $role)
    {
        $this->role_type = $role->value;
    }

    public function getJWTIdentifier(): string
    {
        // JWT トークンに保存する ID を返す
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        // JWT トークンに埋め込む追加の情報を返す
        return [];
    }

    public function createNewUser(Array $param): void
    {
        $this->pid = Str::uuid();
        $this->name = $param['name'];
        $this->email = $param['email'];
        $this->password = bcrypt(Str::password());
        $this->role_type = Role::getByName(strtolower($param['role']))->value;
        $this->save();
    }

    public function updateUser(Array $param): void
    {
        $this->name = $param['name'];
        $this->email = $param['email'];
        $this->role_type = Role::getByName($param['role'])->value;
        $this->save();
    }

    public function isAdmin(): bool
    {
        return Role::from($this->role_type)->isAdmin();
    }

    public function isInstructor(): bool
    {
        return Role::from($this->role_type)->isInstructor();
    }

    public function isStudent(): bool
    {
        return Role::from($this->role_type)->isStudent();
    }

    public function sendPasswordResetMail($token, $reset_url)
    {
        $this->notify(new ResetPasswordNotification($token, $reset_url));
    }
}
