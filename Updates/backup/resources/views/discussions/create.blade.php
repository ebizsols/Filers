<div class="modal-header">
    <h5 class="modal-title">@lang('app.new') @lang('modules.projects.discussion')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createMethods" method="POST" class="ajax-form">
            <input type="hidden" name="project_id" value="{{ $projectId }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="my-3">
                        <x-forms.select fieldId="discussion_category_id" :fieldLabel="__('app.category')"
                            fieldName="discussion_category" search="true" fieldRequired="true">
                            @foreach ($categories as $item)
                                <option
                                    data-content="<i class='fa fa-circle mr-2' style='color: {{ $item->color }}'></i> {{ ucwords($item->name) }}"
                                    value="{{ $item->id }}">
                                    {{ ucwords($item->name) }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                </div>

                <div class="col-md-12">
                    <x-forms.text :fieldLabel="__('app.title')" fieldName="title" fieldRequired="true"
                        fieldId="title" />
                </div>
                <div class="col-md-12">
                    <div class="form-group my-3">
                        <x-forms.label :fieldLabel="__('app.description')" fieldRequired="true" fieldId="description">
                        </x-forms.label>
                        <div id="description"></div>
                        <textarea name="description"  id="description-text" class="d-none"></textarea>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-discussion" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    var quill = new Quill('#description', {
        modules: {
            toolbar: [
                [{
                    header: [1, 2, 3, 4, 5, false]
                }],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                ['bold', 'italic', 'underline', 'strike'],
                ['image', 'code-block', 'link'],
                [{
                    'direction': 'rtl'
                }],
                ['clean']
            ],
            "emoji-toolbar": true,
            "emoji-textarea": true,
            "emoji-shortname": true,
        },
        theme: 'snow'
    });


    // save discussion
    $('#save-discussion').click(function() {
        var note = document.getElementById('description').children[0].innerHTML;
        document.getElementById('description-text').value = note;

        $.easyAjax({
            url: "{{ route('discussion.store') }}",
            container: '#createMethods',
            type: "POST",
            blockUI: true,
            data: $('#createMethods').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });

    init('#createMethods');

</script>
