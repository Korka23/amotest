<?php

namespace App\Http\Controllers;

use App\Services\AmoCrmAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WebhookLog;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info(print_r($data, true));

        app(AmoCrmAuthService::class)->loadToken();

        $this->processEvents($data, 'leads');
        $this->processEvents($data, 'contacts');

        return response()->noContent();
    }

    protected function processEvents(array $data, string $type): void
    {
        if (!isset($data[$type])) {
            return;
        }

        foreach (['add', 'update'] as $action) {
            if (!empty($data[$type][$action])) {
                foreach ($data[$type][$action] as $item) {
                    $this->saveLog($data['account']['subdomain'] ?? null, $action, $item['name'] ?? null, $type);
                }
            }
        }
    }

    protected function saveLog(?string $client, string $action, string $name, string $type): void
    {
        WebhookLog::query()->create([
            'client_name' => $client,
            'action' => $action,
            'name' => $name,
            'type' => $type
        ]);
    }
}