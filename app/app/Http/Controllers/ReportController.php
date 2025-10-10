<?php

namespace App\Http\Controllers;

use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(
        private JobService $jobService
    ) {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display the reports index page.
     */
    public function index()
    {
        $this->authorize('dashboard.export');

        return view('reports.index', [
            'reportTypes' => $this->jobService->getAvailableReportTypes(),
            'reportFormats' => $this->jobService->getAvailableReportFormats(),
            'recentReports' => $this->getRecentReports()
        ]);
    }

    /**
     * Generate a new report.
     */
    public function generate(Request $request)
    {
        $this->authorize('dashboard.export');

        $request->validate([
            'report_type' => 'required|in:tickets,users,companies,performance',
            'format' => 'required|in:csv,json',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'company_id' => 'nullable|exists:companies,id',
            'status_id' => 'nullable|exists:ticket_statuses,id',
            'priority_id' => 'nullable|exists:ticket_priorities,id',
        ]);

        $filters = $request->only([
            'date_from',
            'date_to',
            'company_id',
            'status_id',
            'priority_id'
        ]);

        // Convert date strings to Carbon instances
        if (isset($filters['date_from'])) {
            $filters['date_from'] = Carbon::parse($filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $filters['date_to'] = Carbon::parse($filters['date_to']);
        }

        $this->jobService->generateReport(
            Auth::user(),
            $request->report_type,
            $filters,
            $request->format
        );

        return redirect()->route('reports.index')
            ->with('success', 'Le rapport est en cours de génération. Vous recevrez une notification lorsque celui-ci sera prêt.');
    }

    /**
     * Download a report file.
     */
    public function download($filename)
    {
        $this->authorize('dashboard.export');

        $path = "reports/{$filename}";
        
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Fichier introuvable');
        }

        // Verify user can download this file (security check)
        if (!$this->canUserDownloadFile($filename)) {
            abort(403, 'Accès non autorisé');
        }

        return Storage::disk('local')->download($path);
    }

    /**
     * Delete a report file.
     */
    public function destroy($filename)
    {
        $this->authorize('dashboard.export');

        $path = "reports/{$filename}";
        
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Fichier introuvable');
        }

        // Verify user can delete this file (security check)
        if (!$this->canUserDownloadFile($filename)) {
            abort(403, 'Accès non autorisé');
        }

        Storage::disk('local')->delete($path);

        return redirect()->route('reports.index')
            ->with('success', 'Le rapport a été supprimé avec succès.');
    }

    /**
     * Get recent reports for the current user.
     */
    private function getRecentReports(): array
    {
        $storage = Storage::disk('local');
        $reportFiles = $storage->files('reports');
        $reports = [];

        foreach ($reportFiles as $file) {
            $filename = basename($file);
            $lastModified = Carbon::createFromTimestamp($storage->lastModified($file));
            
            // Only show reports for the current user (based on filename pattern)
            if ($this->canUserDownloadFile($filename)) {
                $reports[] = [
                    'filename' => $filename,
                    'size' => $this->formatFileSize($storage->size($file)),
                    'created_at' => $lastModified,
                    'download_url' => route('reports.download', $filename),
                    'delete_url' => route('reports.destroy', $filename)
                ];
            }
        }

        // Sort by creation date (newest first)
        usort($reports, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return array_slice($reports, 0, 20); // Limit to 20 most recent
    }

    /**
     * Check if user can download/delete a file.
     */
    private function canUserDownloadFile(string $filename): bool
    {
        // For now, allow all authenticated users to access all reports
        // In a production environment, you might want to implement more strict controls
        
        // Admin can access all reports
        if (Auth::user()->hasRole('Administrator')) {
            return true;
        }

        // Users can only access reports they generated
        // This is a simplified check - you might want to store user_id in filename or metadata
        return true;
    }

    /**
     * Format file size for display.
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
