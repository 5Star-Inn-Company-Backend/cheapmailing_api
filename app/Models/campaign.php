<?php

namespace App\Models;

use App\Models\tags;
use App\Models\business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'title',
        'recipient',
        'from',
        'subject',
        'content',
    ];

    public function business(){
        return $this->belongsTo(business::class);
    }

    public function tags(){
        return $this->belongsTo(tags::class);
    }
}
