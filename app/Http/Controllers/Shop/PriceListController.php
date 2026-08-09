<?php

namespace App\Http\Controllers\Shop;

use App\Exports\PriceListExport;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;

class PriceListController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth', 'b2b.access'];
    }

    public function download()
    {
        return PriceListExport::download(auth()->id());
    }
}