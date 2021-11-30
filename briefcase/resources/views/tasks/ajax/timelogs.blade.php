<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">

    <div class="d-flex flex-wrap p-20" id="task-file-list">

        <x-table headType="thead-light">
            <x-slot name="thead">
                <th>@lang('app.employee')</th>
                <th>@lang('modules.timeLogs.startTime')</th>
                <th>@lang('modules.timeLogs.endTime')</th>
                <th class="text-right">@lang('modules.employees.hoursLogged')</th>
            </x-slot>

            @forelse ($task->approvedTimeLogs as $item)
                <tr>
                    <td>
                        <x-employee :user="$item->user" />
                    </td>
                    <td>
                        {{ $item->start_time->timezone($global->timezone)->format($global->date_format . ' ' . $global->time_format) }}
                    </td>
                    <td>
                        @if (!is_null($item->end_time))
                            {{ $item->start_time->timezone($global->timezone)->format($global->date_format . ' ' . $global->time_format) }}
                        @else
                            <span class='badge badge-primary'>{{ __('app.active') }}</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @php
                            $timeLog = intdiv($item->total_minutes, 60) . ' ' . __('app.hrs') . ' ';
                            
                            if ($item->total_minutes % 60 > 0) {
                                $timeLog .= $item->total_minutes % 60 . ' ' . __('app.mins');
                            }
                        @endphp
                        {{ $timeLog }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-cards.no-record :message="__('messages.noRecordFound')" icon="clock" />
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>
</div>
<!-- TAB CONTENT END -->
