<?php

namespace App\Http\Controllers;

use App\Services\AmoCrmAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $client = (new AmoCrmAuthService())->loadToken();
        $data = $request->all();
        Log::info(print_r($data, true));
        return response()->noContent();
    }
}