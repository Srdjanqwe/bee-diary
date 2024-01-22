<?php

namespace App\Models;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['no','division'];

    public function lastHistory()
    {
        return $this->hasOne(History::class)
            ->orderBy('post_id','asc')
            ->orderBy('inspection_date', 'desc');
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }
}
