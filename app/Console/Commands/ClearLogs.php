<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear {--days=7 : Number of days to keep logs} {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old Laravel log files automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');
        
        $logPath = storage_path('logs');
        
        if (!File::exists($logPath)) {
            $this->error('Log directory does not exist!');
            return 1;
        }

        // Get all log files
        $logFiles = File::glob($logPath . '/*.log*');
        $cutoffDate = Carbon::now()->subDays($days);
        $filesToDelete = [];

        foreach ($logFiles as $file) {
            $fileTime = Carbon::createFromTimestamp(File::lastModified($file));
            
            if ($fileTime->lt($cutoffDate)) {
                $filesToDelete[] = $file;
            }
        }

        if (empty($filesToDelete)) {
            $this->info('No old log files found to delete.');
            return 0;
        }

        $this->info("Found " . count($filesToDelete) . " log files older than {$days} days:");
        
        foreach ($filesToDelete as $file) {
            $fileName = basename($file);
            $fileSize = $this->formatBytes(File::size($file));
            $fileDate = Carbon::createFromTimestamp(File::lastModified($file))->format('Y-m-d H:i:s');
            
            $this->line("  - {$fileName} ({$fileSize}) - {$fileDate}");
        }

        if (!$force && !$this->confirm('Do you want to delete these files?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deletedCount = 0;
        $totalSize = 0;

        foreach ($filesToDelete as $file) {
            try {
                $totalSize += File::size($file);
                File::delete($file);
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete " . basename($file) . ": " . $e->getMessage());
            }
        }

        $this->info("Successfully deleted {$deletedCount} log files, freed up " . $this->formatBytes($totalSize) . " of disk space.");
        
        return 0;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
