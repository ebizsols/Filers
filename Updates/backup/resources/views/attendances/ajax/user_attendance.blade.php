@forelse ($dateWiseData as $key => $dateData)
    @php
    $currentDate = \Carbon\Carbon::parse($key);
    @endphp
    @if ($dateData['attendance'])

        <tr>
            <td>
                <div class="media-body">
                    <h5 class="mb-0 f-13">{{ $currentDate->format($global->date_format) }}
                    </h5>
                    <p class="mb-0 f-13 text-dark-grey">
                        <label class="badge badge-secondary">{{ $currentDate->format('l') }}</label>
                    </p>
                </div>
            </td>
            <td><label class="badge badge-success">@lang('modules.attendance.present')</label></td>
            <td colspan="3">
                <table width="100%">
                    @foreach ($dateData['attendance'] as $attendance)
                        <tr>
                            <td width="25%" class="al-center bt-border">
                                {{ $attendance->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                            </td>
                            <td width="25%" class="al-center bt-border text-center">
                                @if (!is_null($attendance->clock_out_time))
                                    {{ $attendance->clock_out_time->timezone($global->timezone)->format($global->time_format) }}
                                @else - @endif
                            </td>
                            <td class="bt-border al-center text-right" style="padding-bottom: 5px;">
                                <x-forms.button-secondary icon="search" class="view-attendance"
                                    data-attendance-id="{{ $attendance->aId }}">
                                    @lang('app.details')
                                </x-forms.button-secondary>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>

        </tr>
    @else
        <tr>
            <td>
                <div class="media-body">
                    <h5 class="mb-0 f-13">{{ $currentDate->format($global->date_format) }}
                    </h5>
                    <p class="mb-0 f-13 text-dark-grey">
                        <label class="badge badge-secondary">{{ $currentDate->format('l') }}</label>
                    </p>
                </div>
            </td>
            <td>
                @if (!$dateData['holiday'] && !$dateData['leave'])
                    <label class="badge badge-danger">@lang('modules.attendance.absent')</label>
                @elseif($dateData['leave'])
                    @if ($dateData['leave']['duration'] == 'half day')
                        <label class="badge badge-primary">@lang('modules.attendance.leave')</label><br><br>
                        <label class="badge badge-warning">@lang('modules.attendance.halfDay')</label>
                    @else
                        <label class="badge badge-primary">@lang('modules.attendance.leave')</label>
                    @endif
                @else
                    <label class="badge badge-secondary">@lang('modules.attendance.holiday')</label>
                @endif
            </td>
            <td colspan="3">
                <table width="100%">
                    <tr>
                        <td width="25%">-</td>
                        <td width="25%" class="text-center">-</td>
                        <td class="text-right" style="padding-bottom: 5px;">
                            @if ($dateData['holiday'] && !$dateData['leave'])
                                @lang('modules.attendance.holidayfor') {{ ucwords($dateData['holiday']->occassion) }}
                            @elseif($dateData['leave'])
                                @lang('modules.attendance.leaveFor') {{ ucwords($dateData['leave']['reason']) }}
                            @else
                                -
                            @endif

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="5">
            <x-cards.no-record icon="calendar" :message="__('messages.noRecordFound')" />
        </td>
    </tr>
@endforelse
