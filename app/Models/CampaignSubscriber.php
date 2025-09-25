<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignSubscriber extends Model
{
    use HasFactory;

    protected $connection = 'mysql_service';

    protected $table = 'campaign_subscriber';
}
