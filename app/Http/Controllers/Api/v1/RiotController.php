<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Logging\FileLog;
use App\Models\Api\V1\Rotation;

class RiotController extends Controller
{
    /**
     * Retrieval of information concerning maintenance and incidents
     *
     * @return json maintenances and incidents data
     */
    public function getPlatform() {
        try{
            $response = Http::withHeaders([
                'X-Riot-Token' => env('RIOT_KEY_API'),
            ])->get(env('RIOT_URL_PLATFORM'));

            // Returns the error that Riot sent
            $checkResponseApi = $response['status']['status_code'] ?? null;
            if($checkResponseApi) return $checkResponseApi;

            $maintenances = [];
            foreach($response['maintenances'] as $maintenance) {
                $maintenances[] = [
                    'title' =>  $maintenance['titles'][0]['content'] ?? null,
                    'created_at' => $maintenance['updates'][0]['created_at'] ?? null,
                    'updated_at' => $maintenance['updates'][0]['updated_at'] ?? null,
                    'author' => $maintenance['updates'][0]['author'] ?? null,
                    'message' => $maintenance['updates'][0]['translations'][0]['content'] ?? null,
                    'maintenance_status' => $maintenance['maintenance_status'] ?? null,
                    'platform' => $maintenance['platforms'] ?? null,
                    'incident_severity' => $maintenance['incident_severity'] ?? null,
                ];
            }
            $results  = [
                'maintenances' => $maintenances,
                'incidents' => $response['incidents']?? null,
            ];
            return response()->json(['status' => 200, 'data' => $results]);
        } catch (\Exception $e) {
            //dd($e); // DEBUGG
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'getPlatform',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'controller'
            ];
            FileLog::log('error', $logs, $e);
            return response()->json(['status' => 500, 'message' => 'An error has occurred']);
        }
    }

    /**
     * Retrieves and saves champion rotation. 
     * If a rotation has already been recorded for the week, returns the recorded information
     * A rotation takes place every Tuesday at 2 am
     *
     * @return json rotation data
     */
    public function getChampion() {
        try{
            $lastTuesday = date("Y-m-d 02:00:00",strtotime('last Tuesday'));
            $nextTuesday = date("Y-m-d 01:59:59",strtotime('next Tuesday'));

            // Check if a rotation is already saved and returns it
            $rotation = (new Rotation)->whereBetween('created_at', [$lastTuesday, $nextTuesday])->get();
            if(count($rotation) > 0) {
                return response()->json([
                    'status' => 200, 
                    'message' => "Rotation of the week already saved on {$rotation[0]->created_at}", 
                    'data' => json_decode($rotation[0]->data)
                ]);
            }

            $response = Http::withHeaders([
                'X-Riot-Token' => env('RIOT_KEY_API'),
                ])->get(env('RIOT_URL_CHAMPION'));

            $checkResponseApi = $response['status']['status_code'] ?? null;
            if($checkResponseApi) return $checkResponseApi;

            (new Rotation)->insert([
                'data' => $response,
            ]);
            return response()->json(['status' => 200,'data' => json_decode($response)]);
        } catch (\Exception $e) {
            //dd($e); // DEBUGG
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'getChampion',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'controller'
            ];
            FileLog::log('error', $logs, $e);
            return response()->json(['status' => 500, 'message' => 'An error has occurred']);
        }
    }
}