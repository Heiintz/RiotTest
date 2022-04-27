<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Logging\FileLog;

class RiotController extends Controller
{
    public function getPlatform() {
        try{
            $response = Http::withHeaders([
                'X-Riot-Token' => env('RIOT_KEY_API'),
            ])->get('https://euw1.api.riotgames.com/lol/status/v4/platform-data', []);
            if($response) {
                $results  = [
                    'maintenance' => [
                        'created_at' => $response['maintenances'][0]['updates'][0]['created_at'] ?? null,
                        'author' => $response['maintenances'][0]['updates'][0]['author'] ?? null,
                        'message' => $response['maintenances'][0]['updates'][0]['translations'][4]['content'] ?? null,
                        'updated_at' => $response['maintenances'][0]['updates'][0]['updated_at'] ?? null,
                        'maintenance_status' => $response['maintenances'][0]['maintenance_status'] ?? null,
                        'platform' => $response['maintenances'][0]['platforms'] ?? null,
                        'incident_severity' => $response['maintenances'][0]['incident_severity'] ?? null,
                    ],
                    'incidents' => $response['incidents']?? null,
                ];
                return json_encode($results);
            }
            return 'Aucune données disponibles.';
        } catch (\Exception $e) {
            dd($e);
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'getPlatform',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'administration'
            ];

            FileLog::log('error', $logs, $e);
        }
    }
}