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

                @if ($user->id == user()->id || in_array('admin', user_roles()))
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

            <div class="d-flex flex-wrap">
            @foreach ($message->files as $file)
                <x-file-card :fileName="$file->filename"
                    :dateAdded="$file->created_at->diffForHumans()">
                    @if ($file->icon == 'images')
                        <img src="{{ $file->file_url }}">
                    @else
                        <i class="fa {{ $file->icon }} text-lightest"></i>
                    @endif

                    <x-slot name="action">
                        <div class="dropdown ml-auto file-action">
                            <button
                                class="btn btn-lg f-14 p-0 text-lightest text-capitalize rounded  dropdown-toggle"
                                type="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                aria-labelledby="dropdownMenuLink" tabindex="0">
                                @if ($file->icon != 'images')
                                    <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 "
                                        target="_blank"
                                        href="{{ $file->file_url }}">@lang('app.view')</a>
                                @endif

                                <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                    href="{{ route('message_file.download', md5($file->id)) }}">@lang('app.download')</a>

                                @if (user()->id == $user->id)
                                    <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-file"
                                        data-row-id="{{ $file->id }}"
                                        href="javascript:;">@lang('app.delete')</a>
                                @endif
                            </div>
                        </div>
                    </x-slot>
                </x-file-card>
            @endforeach
            </div>

        </div>

    </div>
</div><!-- card end -->

<script>
    $('body').on('click', '.delete-file', function() {
        var id = $(this).data('row-id');
        var messageFile = $(this);
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('message-file.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            messageFile.closest('.card').remove();
                        }
                    }
                });
            }
        });
    });
</script>
