<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientMealPlanningController extends Controller
{
    public function getRecordsByDateRange(Request $request){
        //validation of request params
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Retrieve records between the start_date and end_date
        $records = DB::table('patient_meal_planning')
            ->whereBetween('planned_date', [$startDate, $endDate])
            ->get();

        return response()->json($records);
    }
    public function getPlanningSummary(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $data = [];

        // Loop through each month between the start_date and end_date
        for ($date = $startDate->copy(); $date->lessThanOrEqualTo($endDate); $date->addMonth()) {
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            // Get records for the current month
            $records = DB::table('patient_meal_planning')
                ->whereBetween('planned_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get();

            // Calculate required metrics
            $totalDays = $monthEnd->diffInDays($monthStart) + 1;
            $plannedDays = $records->count();
            $plannedPercentage = ($plannedDays / $totalDays) * 100;
            $avgTotalCalories = $records->avg('total_calories') ?? 0;
            $avgTotalCarbs = $records->avg('total_carbs') ?? 0;
            $avgTotalProteins = $records->avg('total_proteins') ?? 0;
            $avgTotalFats = $records->avg('total_fats') ?? 0;

            // Calculate days planning was skipped
            $plannedDates = $records->pluck('planned_date')->toArray();
            $daysSkipped = [];

            for ($d = $monthStart->copy(); $d->lessThanOrEqualTo($monthEnd); $d->addDay()) {
                if (!in_array($d->toDateString(), $plannedDates)) {
                    $daysSkipped[] = $d->format('d F Y');
                }
            }

            // Prepare the data for the current month as required format....
            $data[] = [
                'month' => $date->format('F Y'),
                'planned_percentage' => number_format($plannedPercentage, 2) . ' %',
                'avg_total_calories' => round($avgTotalCalories, 2),
                'avg_total_carbs' => round($avgTotalCarbs, 2),
                'avg_total_protein' => round($avgTotalProteins, 2),
                'avg_total_fat' => round($avgTotalFats, 2),
                'days_planning_skipped' => $daysSkipped,
            ];
        }

        return response()->json(['data' => $data]);
    }

}
