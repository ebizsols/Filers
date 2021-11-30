@php
$addSubTaskPermission = user()->permission('add_sub_tasks');
$editSubTaskPermission = user()->permission('edit_sub_tasks');
$deleteSubTaskPermission = user()->permission('delete_sub_tasks');
$viewSubTaskPermission = user()->permission('view_sub_tasks');
@endphp

<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">

    @if ($addSubTaskPermission == 'all' || $addSubTaskPermission == 'added')
        <div class="p-20">

            <div class="row">
                <div class="col-md-12">
                    <a class="f-15 f-w-500" href="javascript:;" id="add-sub-task"><i
                            class="icons icon-plus font-weight-bold mr-1"></i>@lang('app.add')
                        @lang('modules.tasks.subTask')</a>
                </div>
            </div>

            <x-form id="save-subtask-data-form" class="d-none">
                <input type="hidden" name="task_id" value="{{ $task->id }}">
                <div class="row">
                    <div class="col-md-8">
                        <x-forms.text :fieldLabel="__('app.title')" fieldName="title" fieldRequired="true"
                            fieldId="title" :fieldPlaceholder="__('placeholders.task')" />
                    </div>

                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="task_due_date" :fieldLabel="__('app.dueDate')" fieldName="due_date"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>
                    <div class="col-md-12">
                        <div class="w-100 justify-content-end d-flex mt-2">
                            <x-forms.button-cancel id="cancel-subtask" class="border-0 mr-3">@lang('app.cancel')
                            </x-forms.button-cancel>
                            <x-forms.button-primary id="save-subtask" icon="location-arrow">@lang('app.submit')
                                </x-button-primary>
                        </div>
                    </div>
                </div>
            </x-form>
        </div>
    @endif


    @if ($viewSubTaskPermission == 'all' || $viewSubTaskPermission == 'added')
        <div class="d-flex flex-wrap justify-content-between p-20" id="sub-task-list">
            @forelse ($task->subtasks as $subtask)
                <div class="card w-100 rounded-0 border-0 subtask mb-3">

                    <div class="card-horizontal">
                        <div class="d-flex">
                            <x-forms.checkbox :fieldId="'checkbox'.$subtask->id" class="task-check"
                                data-sub-task-id="{{ $subtask->id }}"
                                :checked="($subtask->status == 'complete') ? true : false" fieldLabel=""
                                :fieldName="'checkbox'.$subtask->id" />

                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex flex-grow-1">
                                <p class="card-title f-14 mr-3 text-dark">
                                    {!! $subtask->status == 'complete' ? '<s>' . ucfirst($subtask->title) . '</s>' : ucfirst($subtask->title) !!}
                                </p>
                                <div class="dropdown ml-auto subtask-action">
                                    <button
                                        class="btn btn-lg f-14 p-0 text-lightest text-capitalize rounded  dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                        aria-labelledby="dropdownMenuLink" tabindex="0">
                                        @if ($editSubTaskPermission == 'all' || ($editSubTaskPermission == 'added' && $subtask->added_by == user()->id))
                                            <a class="dropdown-item edit-subtask"
                                                href="javascript:;"
                                                data-row-id="{{ $subtask->id }}">@lang('app.edit')</a>
                                        @endif

                                        @if ($deleteSubTaskPermission == 'all' || ($deleteSubTaskPermission == 'added' && $subtask->added_by == user()->id))
                                            <a class="dropdown-item delete-subtask"
                                                data-row-id="{{ $subtask->id }}"
                                                href="javascript:;">@lang('app.delete')</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-text f-11 text-lightest text-justify">
                                {{ $subtask->due_date ? __('modules.invoices.due') . ': ' . $subtask->due_date->format($global->date_format) : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="align-items-center d-flex flex-column text-lightest p-20 w-100">
                    <i class="fa fa-tasks f-21 w-100"></i>

                    <div class="f-15 mt-4">
                        - @lang('messages.noSubTaskFound') -
                    </div>
                </div>
            @endforelse

        </div>
    @endif

</div>
<!-- TAB CONTENT END -->

<script>
    $(document).ready(function() {

        datepicker('#task_due_date', {
            position: 'bl',
            ...datepickerConfig
        });

        $('#save-subtask').click(function() {

            const url = "{{ route('sub-tasks.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-subtask-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-subtask",
                data: $('#save-subtask-data-form').serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        window.location.reload();
                    }
                }
            });
        });

        $('body').on('click', '#add-sub-task', function() {
            $(this).closest('.row').addClass('d-none');
            $('#save-subtask-data-form').removeClass('d-none');
        });


        $('#cancel-subtask').click(function() {
            $('#save-subtask-data-form').addClass('d-none');
            $('#add-sub-task').closest('.row').removeClass('d-none');
        });


    });
</script>
