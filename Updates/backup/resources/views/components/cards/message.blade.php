<div class="card ticket-message rounded-0 border-0  @if (user()->id == $user->id) bg-white-shade @endif" id="message-{{ $message->id }}">
    <div class="card-horizontal">
        <div class="card-img">
            <a
                href="{{ !is_null($user->employeeDetail) ? route('employees.show', $user->id) : route('clients.show', $user->id) }}"><img
                    class="" src="{{ $user->image_url }}" alt="{{ $user->name }}"></a>
        </div>
        <div class="card-body border-0 pl-0">
            <div class="d-flex">
                <a href="{{ !is_null($user->employeeDetail) ? route('employees.show', $user->id) : route('clients.show', $user->id) }}">
                    <h4 class="card-title f-15 f-w-500 text-dark mr-3">{{ $user->name }}</h4>
                </a>
                <p class="card-date f-11 text-lightest mb-0">
                    {{ $message->created_at->timezone(global_setting()->timezone)->format(global_setting()->date_format . ' ' . global_setting()->time_format) }}
                </p>

                @if ($user->id == user()->id)
                    <div class="dropdown ml-auto message-action">
                        <button class="btn btn-lg f-14 p-0 text-lightest text-capitalize rounded  dropdown-toggle"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">

                            <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 delete-message"
                                data-row-id="{{ $message->id }}" href="javascript:;">@lang('app.delete')</a>
                        </div>
                    </div>
                @endif

            </div>
            <p class="card-text text-dark-grey text-justify">{!! nl2br($message->message) !!}</p>

            {{ $slot }}

        </div>

    </div>
</div><!-- card end -->
