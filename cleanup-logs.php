<?php
/**
 * Simple Laravel Log Cleanup Script
 * Usage: php cleanup-logs.php [days]
 * Example: php cleanup-logs.php 7
 */

$days = isset($argv[1]) ? (int)$argv[1] : 7;
$logPath = __DIR__ . '/storage/logs';

if (!is_dir($logPath)) {
    echo "Log directory not found: {$logPath}\n";
    exit(1);
}

$cutoffTime = time() - ($days * 24 * 60 * 60);
$logFiles = glob($logPath . '/*.log*');
$deletedFiles = 0;
$totalSize = 0;

echo "Cleaning up log files older than {$days} days...\n";
echo "Log directory: {$logPath}\n\n";

foreach ($logFiles as $file) {
    if (filemtime($file) < $cutoffTime) {
        $fileSize = filesize($file);
        $fileName = basename($file);
        $fileDate = date('Y-m-d H:i:s', filemtime($file));
        
        echo "Deleting: {$fileName} (" . formatBytes($fileSize) . ") - {$fileDate}\n";
        
        if (unlink($file)) {
            $deletedFiles++;
            $totalSize += $fileSize;
        } else {
            echo "  ERROR: Failed to delete {$fileName}\n";
        }
    }
}

if ($deletedFiles > 0) {
    echo "\nCleanup completed!\n";
    echo "Deleted files: {$deletedFiles}\n";
    echo "Freed space: " . formatBytes($totalSize) . "\n";
} else {
    echo "\nNo old log files found to delete.\n";
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
