<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Relations\HasMany;

class Host extends Model
{
    use HasFactory;

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    } 

    protected $fillable = [
        'name',
        'bio',
        'avatar_url',
    ];
}
