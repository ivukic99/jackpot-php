<?php
class ErrorLogger {
    public static function error($message, $file, $line): void {
        $log = "[" . date('d-m-Y H:i:s') . "] ERROR: $message on $file in $line" . PHP_EOL;
        error_log($log, 3, '/var/www/html/logs/error.log');
    }
}