<?php

namespace App\Console\Commands\Exports\Finance;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\Exports\Csvstructure\Facturation;
use App\Console\Commands\Exports\DatabaseOperation;
use App\Logging\FileLog;

class FacturationOrange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:FacturationOrange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Récupération des données pour la facturation d\'Orange';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $query = "
                SELECT 
                    t2.accesftth_id, 
                    t6.reference_pm,
                    t6.etat_mad_ligne, 
                    t3.code as 'NomDSP', 
                    '???' as 'ServiceId', 
                    t0.reference_oc as 'RefCmdClient', 
                    t2.reference_iftr as 'RefCmdIftr',
                    t9.reference_prestation_prise as 'RefCmdOrange', 
                    DATE_FORMAT(STR_TO_DATE(t6.date_raccordement_prise,'%Y%m%d %H:%i'),'%d/%m/%Y %H:%i')  as 'DateRaccordement', 
                    DATE_FORMAT(t6.date_creation,'%d/%m/%Y %H:%i')  as 'DateArriveeRaccordement',
                    DATE_FORMAT(STR_TO_DATE(t8.date_annulation,'%Y%m%d %H:%i'),'%d/%m/%Y %H:%i') as 'DateResiliation', 
                    IF(t11.notifreprov_id is not null AND t11.date_creation >= t6.date_creation, 
                    t11.reference_prise, t6.reference_prise) as 'RefPTO', 
                    t6.type_pbo as 'TypeAdduction',
                    t5.value as 'PriseExistante'
                FROM cmdacces t0
                INNER JOIN acces t1 ON t1.accesftth_id = t0.accesftth_id
                INNER JOIN accesftth t2 ON t2.accesftth_id = t0.accesftth_id
                INNER JOIN operateur t3 ON t3.operateur_id = t0.operateur_id
                INNER JOIN client t4 ON t4.client_id = t0.client_id
                LEFT JOIN prise_existante t5 ON t5.prise_existante_id = t1.prise_existante_id
                INNER JOIN crmadl t6 ON t6.accesftth_id = t0.accesftth_id
                INNER JOIN (SELECT accesftth_id, MAX(crmadl_id) as 'crmadl_id'
                FROM crmadl 
                WHERE etat_mad_ligne = ?
                GROUP BY accesftth_id) t7 ON t7.accesftth_id = t0.accesftth_id AND t7.crmadl_id = t6.crmadl_id
                LEFT JOIN annul t8 ON t8.accesftth_id = t0.accesftth_id
                LEFT JOIN cr_acces t9 ON t9.accesftth_id = t0.accesftth_id
                INNER JOIN (SELECT accesftth_id, MAX(cr_acces_id) as 'cr_acces_id'
                FROM cr_acces 
                WHERE UPPER(etat_cr_commande_prise) = ?
                GROUP BY accesftth_id) t10 ON t10.accesftth_id = t0.accesftth_id AND t10.cr_acces_id = t9.cr_acces_id
                LEFT JOIN  (SELECT accesftth_id, MAX(notifreprov_id) as 'notifreprov_id'
                FROM notifreprov 
                GROUP BY accesftth_id) t12 ON t12.accesftth_id = t0.accesftth_id 
                LEFT JOIN notifreprov t11 ON t11.accesftth_id = t0.accesftth_id AND t12.notifreprov_id = t11.notifreprov_id
                WHERE t3.operateur_exploitant_id = ?
                ORDER BY t3.code, t6.date_raccordement_prise
            ;";
            
            $params = [
                "OK", 
                "OK",
                4
            ];

            $facturationOrange = DB::select($query, $params);

            $fileName = "EXPORT_FacturationOrange_".date('Ymd_His').".csv";
            $filePath = $_ENV['EXPORT_FOLDER'] . '/'. $fileName;
            
            $file = fopen($filePath, 'w');
            fputcsv($file, Facturation::$Orange['column']);

            foreach($facturationOrange as $facturation) {
                fputcsv($file, array(
                    $facturation->accesftth_id,
                    $facturation->reference_pm,
                    $facturation->etat_mad_ligne,
                    $facturation->NomDSP,
                    $facturation->ServiceId,
                    $facturation->RefCmdClient,
                    $facturation->RefCmdIftr, 
                    $facturation->RefCmdOrange, 
                    $facturation->DateRaccordement,
                    $facturation->DateArriveeRaccordement,
                    $facturation->DateResiliation,
                    $facturation->RefPTO,
                    $facturation->TypeAdduction,
                    $facturation->PriseExistante
                ));
            }

            DatabaseOperation::create()->insertExport(
                'Finance',
                'Fixe',
                $fileName,
                $filePath,
                $query,
                $params,
                null,
                null,
                null,
                null,
                null,
                'Le premier jour du mois',
                $this->signature
            );
        } catch (\Exception $e) {
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'FacturationOrange',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'Exports'
            ];

            FileLog::log('error', $logs, $e);
        }
    }
}
