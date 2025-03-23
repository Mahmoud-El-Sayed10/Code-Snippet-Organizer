<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Language;
use App\Models\Tag;

class Snippet extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'title',
        'code_content',
        'language_id',
        'description',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function language(){
        return $this->belongsTo(Language::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function favoritedBy(){
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopeByUser($query, $userId){
        return $query->where('user_id', $userId);
    }

    public function scopeByLanguage($query, $languageId){
        return $query->where('language_id', $languageId);
    }

}
