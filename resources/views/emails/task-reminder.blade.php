@component('mail::message')
    # Task Reminder

    **Task:** {{ $task->title }}

    @if ($task->due_date)
        **Due:** {{ $task->due_date->toFormattedDateString() }}
    @endif

    @component('mail::button', ['url' => route('projects.show', $task->project_id)])
        Open Project
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
