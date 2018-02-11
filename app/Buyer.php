<?php

namespace App;

use App\Transaction;

class Buyer extends User
{
    protected static function boot(){
        parent::boot();

        static::addGlobalScope(new \App\Scopes\BuyerScope);
    }
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}
