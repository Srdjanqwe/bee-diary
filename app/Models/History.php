<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class History extends Model
{
    use HasFactory;

    protected $fillable = ['post_id','content','inspection_date'];

    protected $dates = [
        'inspection_date',
    ];

    public $timestamps = false;

    public function post()
    { 
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function setInspectionDateAttribute($value)
    {
        $this->attributes['inspection_date'] = Carbon::parse($value)->toDateString();
    }

}
