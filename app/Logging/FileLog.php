<?php

namespace App\Logging;

use Exception;
use Illuminate\Support\Facades\Log;

class FileLog
{
    private static string $_logPath;
    private static string $_extension;
    private static string $_logProjectFolder;
    private static string $_class;

    private const LOG_FOLDER_MODE = 0744;
    private const LOG_FILE_MODE = 0644;

    public static function log (string $extension, array $logs, $e = null) {
        self::$_logProjectFolder = $logs['logProjectFolder'];
        self::$_class = strtolower(end($logs['class']));
        self::$_extension = $extension;

        if ($e) {
            self::setErrorLogs($logs, $e);
        }

        $encodedLogs = json_encode($logs);

        self::$_logPath = self::createLogPath();

        self::writeLog($encodedLogs);
    }

    private static function setErrorLogs (&$logs, $e) {
        $errorLogs = [
            'message' => $e->getMessage() ?? null,
            'code' => $e->getCode() ?? null,
            'file' => $e->getFile() ?? null,
            'trace' => $e->getTrace() ?? null,
            'toString' => $e->__toString() ?? null
        ];

        $logs['error'] = json_encode($errorLogs);
    }

    private static function createLogPath () {
        $day = date('d');
        $month = date('m');
        $year = date('Y');

        $logPath = env('LOG_DIR') . self::$_logProjectFolder . '/' . self::$_class . '/' . $year . '/' . $month . '/' . $day . '.' . self::$_extension;

        try {
            self::createLogPathFolders($logPath);
        } catch (Exception $e) {
            $errorLogs = [];

            self::setErrorLogs($errorLogs, $e);
            self::writeLaravelLog(json_encode($errorLogs));
        }

        return $logPath;
    }

    private static function createLogPathFolders (string $logPath) {
        if (substr_count($logPath, "/")) {
            $dirPath = explode("/", $logPath);
            array_pop($dirPath);

            $dirPath = join("/", $dirPath);
            if (!is_dir($dirPath)) {

                mkdir($dirPath, 0777, true);

                if (mkdir($dirPath, self::LOG_FOLDER_MODE, true) === false) {
                    throw new Exception("Le dossier " . $dirPath . " n'a pas pu être créé");
                }
            }
        }
    }

    private static function writeLog (string $content) {
        self::writeLogstashLog($content);
        self::writeLaravelLog($content);
    }

    private static function writeLogstashLog (string $content) {
        if (!file_put_contents(self::$_logPath, $content . "\n", FILE_APPEND)) {
            throw new Exception("Le fichier " . self::$_logPath . " n'a pas pu être créé.");
        }

        if (!chmod(self::$_logPath, self::LOG_FILE_MODE)) {
            throw new Exception("Le fichier " . self::$_logPath . " n'a pas pu être créé avec les bons droits.");
        }
    }

    private static function writeLaravelLog (string $content) {
        Log::{self::$_extension}($content);
    }
}
