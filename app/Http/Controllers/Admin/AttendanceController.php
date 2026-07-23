<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $date = Carbon::parse($request->query('date', now()->toDateString()))->startOfDay();

        $waiters = Worker::query()
            ->whereIn('role', ['Waiter', 'Waitress'])
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $marked = Attendance::query()
            ->whereDate('date', $date)
            ->whereIn('worker_id', $waiters->pluck('id'))
            ->get()
            ->keyBy('worker_id');

        $presentCount = $marked->where('status', Attendance::STATUS_PRESENT)->count()
            + $marked->where('status', Attendance::STATUS_LATE)->count()
            + $marked->where('status', Attendance::STATUS_HALF_DAY)->count();

        return view('admin-attendance', [
            'date' => $date,
            'waiters' => $waiters,
            'marked' => $marked,
            'statuses' => Attendance::statuses(),
            'presentCount' => $presentCount,
            'absentCount' => $marked->where('status', Attendance::STATUS_ABSENT)->count(),
        ]);
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $date = Carbon::parse($request->validated('date'))->toDateString();
        $rows = $request->validated('attendances');

        DB::transaction(function () use ($rows, $date, $request): void {
            foreach ($rows as $row) {
                Attendance::query()->updateOrCreate(
                    [
                        'worker_id' => (int) $row['worker_id'],
                        'date' => $date,
                    ],
                    [
                        'status' => $row['status'],
                        'note' => $row['note'] ?? null,
                        'marked_by' => $request->user()->id,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.attendance.index', ['date' => $date])
            ->with('status', 'Attendance saved for '.$date.'.');
    }
}
