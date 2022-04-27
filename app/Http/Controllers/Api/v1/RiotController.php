<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Logging\FileLog;
use App\Models\Api\V1\Rotation;

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
                        'title' =>  $response['maintenances'][0]['titles'][0]['content'] ?? null,
                        'created_at' => $response['maintenances'][0]['updates'][0]['created_at'] ?? null,
                        'updated_at' => $response['maintenances'][0]['updates'][0]['updated_at'] ?? null,
                        'author' => $response['maintenances'][0]['updates'][0]['author'] ?? null,
                        'message' => $response['maintenances'][0]['updates'][0]['translations'][0]['content'] ?? null,
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
            dd($e); // DEBUGG
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'getPlatform',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'controller'
            ];

            FileLog::log('error', $logs, $e);
        }
    }

    public function getTuesdays($y,$m){ 
        $date = "$y-$m-01";
        $first_day = date('N',strtotime($date));
        $first_day = 7 - $first_day + 3;
        $last_day =  date('t',strtotime($date));
        $days = array();
        for($i=$first_day; $i<=$last_day; $i=$i+7 ){
            $days[] = $i;
        }
        return  $days;
    }

    public function getChampion() {
        try{
            $lastTuesday = date("Y-m-d 02:00:00",strtotime('last Tuesday'));
            $nextTuesday = date("Y-m-d 01:59:59",strtotime('next Tuesday'));
            $rotation = (new Rotation)->whereBetween('created_at', [$lastTuesday, $nextTuesday])->get();

            if(count($rotation) > 0) {
                return $rotation[0]->data;
            }

            $response = Http::withHeaders([
                'X-Riot-Token' => env('RIOT_KEY_API'),
            ])->get('https://euw1.api.riotgames.com/lol/platform/v3/champion-rotations', []);

            (new Rotation)->insert([
                'data' => $response,
            ]);
            return $response;
        } catch (\Exception $e) {
            dd($e); // DEBUGG
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'getChampion',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'controller'
            ];

            FileLog::log('error', $logs, $e);
        }
    }
}