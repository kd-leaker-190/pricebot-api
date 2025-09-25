<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Robot extends Model
{
    use SoftDeletes;

    protected $table = 'robots';

    protected $fillable = [
        'user_id',
        'domain',
        'currency',
        'shop_name',
        'wp_consumer_key',
        'wp_consumer_secret',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
