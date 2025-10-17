<?php

namespace App\Models;

class Currency extends BaseModel
{
    protected $fillable = ['base_code', 'code', 'exchange_rate', 'status'];
}
