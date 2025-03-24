<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Snippet;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the snippets for the user.
     */
    public function snippets()
    {
        return $this->hasMany(Snippet::class);
    }

    /**
     * Get the user's favorite snippets.
     */
    public function favorites()
    {
        return $this->belongsToMany(Snippet::class, 'favorites')->withTimestamps();
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeByAdmin($query)
    {
        return $query->where('type_id', 1);
    }

    /**
     * Scope a query to only include manager users.
     */
    public function scopeByManager($query)
    {
        return $query->where('type_id', 2);
    }

    /**
     * Get tags created by the user.
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
