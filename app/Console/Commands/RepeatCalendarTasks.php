<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calendar;
use Carbon\Carbon;

class RepeatCalendarTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:repeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate repeating calendar tasks';

    public function handle()
    {
        $tasks = Calendar::where('repeat_type', '!=', 'none')->get();

        foreach ($tasks as $task) {

            $nextDate = match ($task->repeat_type) {
                'daily'   => Carbon::parse($task->task_date)->addDay(),
                'weekly'  => Carbon::parse($task->task_date)->addWeek(),
                'monthly' => Carbon::parse($task->task_date)->addMonth(),
                default   => null,
            };

            if (!$nextDate) continue;

            // Avoid duplicate task creation
            $exists = Calendar::whereDate('task_date', $nextDate)
                ->where('title', $task->title)
                ->exists();

            if ($exists) continue;

            Calendar::create([
                'title'        => $task->title,
                'task_date'    => $nextDate,
                'repeat_type'  => $task->repeat_type,
                'reminder_at'  => $task->reminder_at,
                'is_completed' => false
            ]);
        }

        $this->info('Repeating calendar tasks generated successfully.');
    }
}
