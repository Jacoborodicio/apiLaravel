<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class SellerTransactionController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $transactions = $seller->products()
                    ->whereHas('transactions')
                    ->with('transactions')
                    ->get()
                    ->pluck('transactions')
                    ->collapse();

        return $this->showAll($transactions);
    }
}
