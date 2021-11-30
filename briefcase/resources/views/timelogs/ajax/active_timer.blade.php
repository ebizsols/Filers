@php
$editTimelogPermission = user()->permission('edit_timelogs');
@endphp
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.projects.activeTimers')</h5>
    <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.task')</th>
            <th>@lang('app.employee')</th>
            <th>@lang('modules.timeLogs.startTime')</th>
            <th>@lang('modules.timeLogs.totalHours')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @foreach ($activeTimers as $key => $item)
            <tr id="timer-{{ $item->id }}">
                <td>{{ $key + 1 }}</td>
                <td>
                    <a href="{{ route('tasks.show', $item->task_id) }}" class="text-darkest-grey">
                        {{ $item->task->heading }}
                    </a>
                </td>
                <td>
                    <x-employee :user="$item->user" />
                </td>
                <td>
                    {{ $item->start_time->timezone($global->timezone)->format($global->date_format . ' ' . $global->time_format) }}
                </td>
                <td>
                    @php
                        $endTime = now();
                        $totalHours = $endTime->diff($item->start_time)->format('%d') * 24 + $endTime->diff($item->start_time)->format('%H');
                        $totalMinutes = $totalHours * 60 + $endTime->diff($item->start_time)->format('%i');
                        
                        $timeLog = intdiv($totalMinutes, 60) . ' ' . __('app.hrs') . ' ';
                        
                        if ($totalMinutes % 60 > 0) {
                            $timeLog .= $totalMinutes % 60 . ' ' . __('app.mins');
                        }
                    @endphp

                    <i data-toggle="tooltip" data-original-title="@lang('app.active')"
                        class="fa fa-hourglass-start"></i> {{ $timeLog }}
                </td>
                <td class="text-right">
                    @if (
                        $editTimelogPermission == 'all'
                        || ($editTimelogPermission == 'added' && $item->added_by == user()->id)
                        || ($editTimelogPermission == 'owned'
                            && (($item->project && $item->project->client_id == user()->id) || $item->user_id == user()->id)
                            )
                        || ($editTimelogPermission == 'both' && (($item->project && $item->project->client_id == user()->id) || $item->user_id == user()->id || $item->added_by == user()->id))
                    )
                        <x-forms.button-secondary class="stop-active-timer" icon="stop-circle"
                            data-time-id="{{ $item->id }}">@lang('app.stop')</x-forms.button-secondary>
                    @endif
                </td>
            </tr>

        @endforeach

    </x-table>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
</div>

<script>
    var activeTimers = parseInt("{{ count($activeTimers) }}");

    $('body').on('click', '.stop-active-timer', function() {
        var id = $(this).data('time-id');
        var url = "{{ route('timelogs.stop_timer', ':id') }}";
        url = url.replace(':id', id);
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: url,
            type: "POST",
            data: {
                timeId: id,
                _token: token
            },
            success: function(data) {
                $('#timer-' + id).remove();
                if (activeTimers == 1) {
                    window.location.reload();
                } else {
                    activeTimers = activeTimers - 1;
                    $('#show-active-timer .active-timer-count').html(activeTimers);
                }
            }
        })

    });

</script>
