<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang("modules.messages.startConversation")</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="createConversationForm">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="d-flex">
                        @if (!in_array('client', user_roles()))
                            @if ($messageSetting->allow_client_employee == 'yes'))
                                <x-forms.radio fieldId="user-type-employee" :fieldLabel="__('app.member')"
                                    fieldValue="employee" fieldName="user_type" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="user-type-client" :fieldLabel="__('app.client')"
                                    fieldValue="client" fieldName="user_type">
                                </x-forms.radio>
                            @else
                                <input type="hidden" name="user_type" value="employee">
                            @endif
                        @endif

                        @if (in_array('client', user_roles()))
                            @if ($messageSetting->allow_client_employee == 'yes' || $messageSetting->allow_client_admin == 'yes')
                                <input type="hidden" name="user_type" value="employee">
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="member-list">
                <div class="form-group">
                    <x-forms.select fieldId="selectEmployee" :fieldLabel="__('modules.messages.chooseMember')"
                        fieldName="user_id" search="true" fieldRequired="true">
                        <option value="">--</option>
                        @foreach ($employees as $item)
                            <option
                                data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'><img class='taskEmployeeImg rounded-circle' src='{{ $item->image_url }}' ></div> {{ ucfirst($item->name) }}</span>"
                                value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>

            @if ($messageSetting->allow_client_admin == 'yes' && !in_array('client', user_roles()))
                <div class="col-md-12 d-none" id="client-list">
                    <div class="form-group">
                        <x-forms.select fieldId="client_id" :fieldLabel="__('modules.client.clientName')"
                            fieldName="client_id" search="true">
                            <option value="">--</option>
                            @foreach ($clients as $item)
                                <option
                                    data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'><img class='taskEmployeeImg rounded-circle' src='{{ $item->image_url }}' ></div> {{ ucfirst($item->name) }}</span>"
                                    value="{{ $item->id }}" @if (isset($client) && $client->id == $item->id) selected @endif>{{ ucwords($item->name) }}
                                </option>
                            @endforeach
                            </select>
                        </x-forms.select>
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <div class="form-group">
                    <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.messages.message')"
                        fieldRequired="true" fieldName="message" fieldId="message"
                        :fieldPlaceholder="__('modules.messages.typeMessage')">
                    </x-forms.textarea>
                </div>
            </div>

        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-message" icon="check">@lang('app.send')</x-forms.button-primary>
</div>

<script>
    $("input[name=user_type]").click(function() {
        $('#member-list, #client-list').toggleClass('d-none');
    });

    $('#save-message').click(function() {
        var url = "{{ route('messages.store') }}";
        $.easyAjax({
            url: url,
            container: '#createConversationForm',
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-message",
            type: "POST",
            data: $('#createConversationForm').serialize(),
            success: function(response) {
                @if (isset($client))
                    let clientId = $('#client_id').val();
                    var redirectUrl = "{{ route('messages.index') }}?clientId="+clientId;
                    window.location.href = redirectUrl;
                @endif

                document.getElementById('msgLeft').innerHTML = response.userList;
                document.getElementById('chatBox').innerHTML = response.messageList;
                $('#sendMessageForm').removeClass('d-none');

                if ($("input[name=user_type]").length > 0 && $("input[name=user_type]").val() ==
                    'client') {
                    var userId = $('#client-list').val();
                } else {
                    var userId = $('#selectEmployee').val();
                }

                $('#current_user_id').val(userId);
                $(MODAL_LG).modal('hide');
            }
        })
    });

    // If request comes from project overview tab where client id is set, then it will select that client name default
    @if (isset($client))
        $("#user-type-client").prop("checked", true);
        $('#member-list, #client-list').toggleClass('d-none');
    @endif

    init('#createConversationForm');
</script>
