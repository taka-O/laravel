<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
use App\Enums\Role;

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
        $new_user = new User;
        $new_user->pid = Str::uuid();
        $new_user->name = $param['name'];
        $new_user->email = $param['email'];
        $new_user->password = bcrypt(Str::password());
        $new_user->role_type = Role::getByName($param['role'])->value;
        $new_user->save();
    }
}
