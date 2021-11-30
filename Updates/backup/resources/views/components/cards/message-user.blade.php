@if ($message->from != user()->id)
    @php
        $user = $message->fromUser;
    @endphp
@else
    @php
        $user = $message->toUser;
    @endphp
@endif

<div class="card rounded-0 border-top-0 border-left-0 border-right-0">
    <a class="tablinks show-user-messages" href="javascript:;" data-name="{{ $user->name }}"
        data-user-id="{{ $user->id }}">
        <div class="card-horizontal">
            <div class="card-img">
                <img class="" src="{{ $user->image_url }}" alt="{{ $user->name }}">
            </div>
            <div class="card-body border-0 pl-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title f-14 f-w-500 text-dark-grey">{{ $user->name }}</h4>
                    <p class="card-date f-11 text-dark-grey mb-0">
                        {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
                </div>
                <p class="card-text f-13 text-lightest">{{ $message->message }}</p>
            </div>
        </div>
    </a>
</div><!-- card end -->
