<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tags extends Model
{
    use HasFactory;

    function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

    function subscribers(){
        return $this->hasMany(subscriber::class, 'tag_id');
    }
}
