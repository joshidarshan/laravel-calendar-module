<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarTask;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class CalendarTaskController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    /**
     * FullCalendar events API
     * Uses calendar visible range (start & end)
     */
    public function events(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end   = Carbon::parse($request->end);

        $tasks = CalendarTask::all();
        $events = [];

        foreach ($tasks as $task) {

            $original = Carbon::parse($task->task_datetime);

            $startDate = $task->start_date
                ? Carbon::parse($task->start_date)->setTimeFrom($original)
                : $original->copy();

            $repeatEnd = $task->repeat_end_date
                ? Carbon::parse($task->repeat_end_date)->endOfDay()
                : null;

            $inRange = function (Carbon $occ) use ($start, $end, $repeatEnd) {
                if ($occ->lt($start) || $occ->gt($end)) return false;
                if ($repeatEnd && $occ->gt($repeatEnd)) return false;
                return true;
            };

            /* ---------------- One-time task ---------------- */
            if ($task->repeat_type === 'none') {
                if ($inRange($original)) {
                    $events[] = $this->formatEvent($task, $original);
                }
                continue;
            }

            /* ---------------- Repeating tasks ---------------- */

            $occ = $startDate->copy();
            if ($occ->lt($start)) {
                $occ = $start->copy()->setTimeFrom($original);
            }

            $safety = 500;

            /* ---------- DAILY ---------- */
            if ($task->repeat_type === 'daily') {
                while ($occ->lte($end) && $safety--) {
                    if ($occ->gte($startDate) && $inRange($occ)) {
                        $events[] = $this->formatEvent($task, $occ);
                    }
                    $occ->addDay();
                }
            }

            /* ---------- WEEKLY ---------- */
            elseif ($task->repeat_type === 'weekly') {

                $weekday = $original->dayOfWeek;
                while ($occ->dayOfWeek !== $weekday) {
                    $occ->addDay();
                }

                while ($occ->lte($end) && $safety--) {
                    if ($occ->gte($startDate) && $inRange($occ)) {
                        $events[] = $this->formatEvent($task, $occ);
                    }
                    $occ->addWeek();
                }
            }

            /* ---------- MONTHLY (FIXED 31→Feb bug) ---------- */
            elseif ($task->repeat_type === 'monthly') {

                $dom = $original->day; // original day (eg 31)

                while ($occ->lte($end) && $safety--) {

                    $validDay = min($dom, $occ->daysInMonth);
                    $occ->day = $validDay;

                    if ($occ->gte($startDate) && $inRange($occ)) {
                        $events[] = $this->formatEvent($task, $occ);
                    }

                    // 🔥 SAFE monthly jump
                    $next = $occ->copy()->addMonthNoOverflow();
                    $occ = $next->setDay(
                        min($dom, $next->daysInMonth)
                    );
                }
            }
        }

        return response()->json($events);
    }

    /* ---------------- Event formatter ---------------- */

    private function formatEvent(CalendarTask $task, Carbon $date)
    {
        $completed = $task->getCompletedOccurrence($date->toDateString());

        return [
            'id'    => $task->id,
            'title' => $task->title,
            'start' => $date->toDateTimeString(),
            'backgroundColor' => $completed ? '#9CA3AF' : $task->color,
            'textColor' => '#fff',
            'extendedProps' => [
                'description'      => $task->description,
                'task_type'        => $task->task_type,
                'repeat_type'      => $task->repeat_type,
                'is_completed'     => (bool) $completed,
                'completed_at'     => $completed['completed_at'] ?? null,
                'completed_note'   => $completed['note'] ?? null,
                'start_date'       => $task->start_date,
                'repeat_end_date'  => $task->repeat_end_date,
                'original_color'   => $task->color,
            ],
        ];
    }

    /* ---------------- CRUD ---------------- */

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'task_datetime'   => 'required|date',
            'start_date'      => 'nullable|date',
            'repeat_end_date' => 'nullable|date|after_or_equal:start_date',
            'task_type'       => ['required', Rule::in(['task','event','meeting','other'])],
            'repeat_type'     => ['required', Rule::in(['none','daily','weekly','monthly'])],
        ]);

        if (empty($data['start_date'])) {
            $data['start_date'] = Carbon::parse($data['task_datetime'])->toDateString();
        }

        CalendarTask::create($data);

        return response()->json(['success' => true]);
    }

    public function update($id, Request $request)
    {
        $task = CalendarTask::findOrFail($id);

        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'task_datetime'   => 'required|date',
            'start_date'      => 'nullable|date',
            'repeat_end_date' => 'nullable|date|after_or_equal:start_date',
            'task_type'       => ['required', Rule::in(['task','event','meeting','other'])],
            'repeat_type'     => ['required', Rule::in(['none','daily','weekly','monthly'])],
        ]);

        if (empty($data['start_date'])) {
            $data['start_date'] = Carbon::parse($data['task_datetime'])->toDateString();
        }

        $task->update($data);

        return response()->json(['success' => true]);
    }

    public function complete($id, Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'note' => 'nullable|string'
    ]);

    $task = CalendarTask::findOrFail($id);

    $task->markCompleted(
        $request->date,
        now(),
        $request->note // ✅ PASS NOTE
    );

    return response()->json(['success' => true]);
}


    public function uncomplete($id, Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $task = CalendarTask::findOrFail($id);
        $task->unmarkCompleted($request->date);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        CalendarTask::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
