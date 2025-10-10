<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOldDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private ?int $daysToKeep = null
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $daysToKeep = $this->daysToKeep ?? config('horizon.trim.recent', 60);
            $cutoffDate = Carbon::now()->subDays($daysToKeep);

            $this->cleanupActivityLogs($cutoffDate);
            $this->cleanupFailedJobs($cutoffDate);
            $this->cleanupOldReports($cutoffDate);

            Log::info('Old data cleanup completed', [
                'cutoff_date' => $cutoffDate->toDateString(),
                'days_to_keep' => $daysToKeep
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Cleanup old activity logs.
     */
    private function cleanupActivityLogs(Carbon $cutoffDate): void
    {
        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();
        
        Log::info('Activity logs cleaned up', [
            'deleted_count' => $deleted,
            'cutoff_date' => $cutoffDate->toDateString()
        ]);
    }

    /**
     * Cleanup old failed jobs.
     */
    private function cleanupFailedJobs(Carbon $cutoffDate): void
    {
        $deleted = \DB::table('failed_jobs')
            ->where('failed_at', '<', $cutoffDate)
            ->delete();
        
        Log::info('Failed jobs cleaned up', [
            'deleted_count' => $deleted,
            'cutoff_date' => $cutoffDate->toDateString()
        ]);
    }

    /**
     * Cleanup old report files.
     */
    private function cleanupOldReports(Carbon $cutoffDate): void
    {
        $storage = \Storage::disk('local');
        $reportFiles = $storage->files('reports');
        
        $deletedCount = 0;
        foreach ($reportFiles as $file) {
            $lastModified = Carbon::createFromTimestamp($storage->lastModified($file));
            
            if ($lastModified < $cutoffDate) {
                $storage->delete($file);
                $deletedCount++;
            }
        }
        
        Log::info('Old report files cleaned up', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateString()
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupOldDataJob failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'cleanup',
            'maintenance'
        ];
    }
}
