<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchPatientMealPlanningSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:patient-meal-planning-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch patient meal planning summary at regular intervals';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $endDate = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::now()->subYears(2)->format('Y-m-d');

        try {
            $response = Http::get(config('app.url') . '/api/patient-meal-planning-summary', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Fetched patient meal planning summary:', ['data' => $data]);
                // Process the data as needed
            } else {
                Log::error('Failed to fetch patient meal planning summary:', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching patient meal planning summary:', ['error' => $e->getMessage()]);
        }

        return Command::SUCCESS;
    }
}
