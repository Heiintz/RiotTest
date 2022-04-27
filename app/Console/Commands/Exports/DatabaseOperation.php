<?php

namespace App\Console\Commands\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DatabaseOperation {

    public static function create() {
        $instance = new self;
        return $instance;
    }

    public function insertExport(
        $groupeExport, $exportType, $fileName, $filePath, $query, $params, $customSelect, $customFrom, $customWhere,
        $dateDebut, $dateFin, $planification, $commandExport
    ) {
        $userIdDefault = $groupeExport === "Finance" ? $_ENV['EXPORT_USER_ID_FINANCE'] : $_ENV['EXPORT_USER_ID_OPERATIONNEL'];
		$userId = Auth::id() ?? $userIdDefault; 

        $queryInsertExport = "CALL sp_ift_insertExport (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $paramsQueryInsertExport = [
            $userId,
            $exportType,
            $fileName,
            $filePath,
            $query,
            serialize($params),
            $customSelect,
            $customFrom,
            $customWhere,
            $dateDebut,
            $dateFin,
            $planification,
            $commandExport
        ];

        $insertExport = DB::select($queryInsertExport, $paramsQueryInsertExport); 
        return $insertExport;
    }
}
