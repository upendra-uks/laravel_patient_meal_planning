<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchPatientMealPlanningRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:patient-meal-planning-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch patient meal planning records for the current month and year at regular intervals';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $currentDate->copy()->endOfMonth()->format('Y-m-d');

        try {
            $response = Http::get(config('app.url') . '/api/patient-meal-planning', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Fetched patient meal planning records:', ['data' => $data]);
                // Process the data as needed
            } else {
                Log::error('Failed to fetch patient meal planning records:', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching patient meal planning records:', ['error' => $e->getMessage()]);
        }

        return Command::SUCCESS;
    }
}