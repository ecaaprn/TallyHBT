<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;

class TimeListController extends Controller
{
    public function update(Request $request, JobOrder $jobOrder)
    {
        $validated = $request->validate([
            'field' => 'required|string|in:plugging,unplugging,open_valve,close_valve',
            'time' => 'required|date_format:H:m',
        ]);

        $jobOrder->timeList()->update([
            $validated['field'] => $validated['time']
        ]);

        return response()->json($jobOrder->load('timeList'));
    }
}
