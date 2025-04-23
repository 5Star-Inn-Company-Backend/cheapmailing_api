<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class tags extends Model
{
    use HasFactory, SoftDeletes;

    function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

    function subscribers(){
        return $this->hasMany(subscriber::class, 'tag_id');
    }
}
