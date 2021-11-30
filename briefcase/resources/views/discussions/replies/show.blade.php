<!-- START -->
<div class="d-flex justify-content-between align-items-center p-3 border-bottom-grey rounded-top bg-white">
    <span>
        <p class="f-15 f-w-500 mb-0">{{ ucfirst($discussion->title) }}</p>
        <p class="f-11 text-lightest mb-0">@lang('modules.tickets.requestedOn')
            {{ $discussion->created_at->timezone($global->timezone)->format($global->date_format . ' ' . $global->time_format) }}
        </p>
    </span>
    <span>
        <p class="mb-0 text-capitalize">
            <x-status :style="'color:'.$discussion->category->color" :value="$discussion->category->name" />
        </p>
    </span>
</div>
<!-- END -->
@foreach ($discussion->replies as $key => $message)
    @php
        $replyUser = $message->user;
    @endphp
    <div class="card ticket-message border-0 rounded-bottom
        @if (user()->id == $replyUser->id) bg-white-shade @endif
        " id="message-{{ $message->id }}">
        <div class="card-horizontal">
            <div class="card-img">
                <a href="{{ route('employees.show', $replyUser->id) }}"><img class=""
                        src="{{ $replyUser->image_url }}" alt="{{ $replyUser->name }}"></a>
            </div>
            <div class="card-body border-0 pl-0">
                <div class="d-flex">
                    <a href="{{ route('employees.show', $replyUser->id) }}">
                        <h4 class="card-title f-15 f-w-500 text-dark mr-3">{{ $replyUser->name }}</h4>
                    </a>
                    <p class="card-date f-11 text-lightest mb-0 mr-3">
                        {{ $message->created_at->timezone(global_setting()->timezone)->format(global_setting()->date_format . ' ' . global_setting()->time_format) }}
                    </p>
                    <div class="dropdown ml-auto message-action">
                        <button class="btn btn-lg f-14 p-0 text-lightest text-capitalize rounded  dropdown-toggle"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">

                            <a class="dropdown-item add-reply" data-row-id="{{ $message->id }}"
                                data-discussion-id="{{ $discussion->id }}" href="javascript:;">@lang('app.reply')</a>

                            @if ($key != 0 && is_null($discussion->best_answer_id) && $discussion->user_id == $replyUser->id)
                                <a class="dropdown-item set-best-answer" data-row-id="{{ $message->id }}"
                                    href="javascript:;">@lang('modules.discussions.bestReply')</a>
                            @endif

                            @if ($replyUser->id == user()->id)
                                <a class="dropdown-item edit-reply" data-row-id="{{ $message->id }}"
                                    data-discussion-id="{{ $discussion->id }}"
                                    href="javascript:;">@lang('app.edit')</a>
                                @if ($key != 0)
                                    <a class="dropdown-item delete-message" data-row-id="{{ $message->id }}"
                                        href="javascript:;">@lang('app.delete')</a>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
                <p class="card-text text-dark-grey text-justify">{!! $message->body !!}</p>


                @if ($discussion->best_answer_id == $message->id)
                    <span class="badge badge-success">@lang('modules.discussions.bestReply')</span>
                @endif


            </div>

        </div>
    </div><!-- card end -->

@endforeach
