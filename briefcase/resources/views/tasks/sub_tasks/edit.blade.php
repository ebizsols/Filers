<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<x-form id="edit-save-subtask-data-form" method="PUT">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('modules.tasks.subTask')</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">

        <input type="hidden" name="task_id" value="{{ $subTask->task_id }}">
        <div class="row">
            <div class="col-md-12">
                <x-forms.text :fieldLabel="__('app.title')" fieldName="title" fieldRequired="true" fieldId="title"
                    :fieldValue="$subTask->title" :fieldPlaceholder="__('placeholders.task')" />
            </div>

            <div class="col-md-12">
                <x-forms.datepicker fieldId="edit_task_due_date" :fieldLabel="__('app.dueDate')" fieldName="due_date"
                    :fieldValue="$subTask->due_date ? $subTask->due_date->format($global->date_format) : ''"
                    :fieldPlaceholder="__('placeholders.date')" />
            </div>
            <div class="col-md-12">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('app.description')" fieldName="description"
                                  fieldId="description" fieldPlaceholder="" :fieldValue="$subTask->description ?? ''">
                </x-forms.textarea>
            </div>
            <div class="col-md-12">
                <a class="f-15 f-w-500" href="javascript:;" id="add-sub-task-file"><i
                        class="fa fa-paperclip font-weight-bold mr-1"></i>@lang('modules.projects.uploadFile')</a>
            </div>
            <div class="col-lg-12 add-file-box-edit d-none">
                <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                                       :fieldLabel="__('modules.projects.uploadFile')" fieldName="file"
                                       fieldId="sub-task-file-upload-dropzone" />
                <input type="hidden" name="image_url" id="image_url">
            </div>
            <div class="col-md-12 add-file-box-edit d-none mb-5">
                <div class="w-100 justify-content-end d-flex mt-2">
                    <x-forms.button-cancel id="cancel-sub-task-file" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </div>
            </div>
            <input type="hidden" name="subTaskID" id="subTaskID">
            <input type="hidden" name="addedFiles" id="addedFiles">
        </div>

    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
        <x-forms.button-primary id="edit-save-subtask" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>
<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>

<script>
    $(document).ready(function() {

        $('#add-sub-task-file').click(function() {
            $('.add-file-box-edit').removeClass('d-none');
            $('#add-sub-task-file').addClass('d-none');
        });

        $('#cancel-sub-task-file').click(function() {
            $('.add-file-box-edit').addClass('d-none');
            $('#add-sub-task-file').removeClass('d-none');
            return false;
        });

        datepicker('#edit_task_due_date', {
            position: 'bl',
            dateSelected: new Date("{{ ($subTask->due_date ? str_replace('-', '/', $subTask->due_date) : str_replace('-', '/', now($global->timezone))) }}"),
            ...datepickerConfig
        });

        Dropzone.autoDiscover = false;
        //Dropzone class
        subTaskDropzone = new Dropzone("div#sub-task-file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('sub-task-files.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            acceptedFiles: dropzoneFileAllow,
            init: function() {
                subTaskDropzone = this;
            }
        });
        subTaskDropzone.on('sending', function(file, xhr, formData) {
            var ids = "{{ $subTask->id }}";
            formData.append('sub_task_id', ids);
            $.easyBlockUI();
        });
        subTaskDropzone.on('uploadprogress', function() {
            $.easyBlockUI();
        });
        subTaskDropzone.on('completemultiple', function(resp) {
            window.location.reload();
        });

        $('#edit-save-subtask').click(function() {

            const url = "{{ route('sub-tasks.update', $subTask->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-save-subtask-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#edit-save-subtask",
                data: $('#edit-save-subtask-data-form').serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        if (subTaskDropzone.getQueuedFiles().length > 0) {
                            subTaskDropzone.processQueue();
                       } else {
                            $('#sub-task-list').html(response.view);
                            $(MODAL_LG).modal('hide');
                        }
                    }
                }
            });
        });

    });

</script>
