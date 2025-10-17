<?php

namespace App\Models;

class MetalRate extends BaseModel
{
    protected $fillable = ['name', 'metal', 'currency', 'exchange', 'symbol', 'prev_close_price', 'open_price', 'low_price', 'high_price', 'open_time', 'price', 'ch', 'chp', 'ask', 'bid', 'price_gram_24k', 'price_gram_22k', 'price_gram_21k', 'price_gram_20k', 'price_gram_18k', 'price_gram_16k', 'price_gram_14k', 'price_gram_10k'];
}
