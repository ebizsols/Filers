@php
$addClientPermission = user()->permission('add_clients');
@endphp

<x-forms.label fieldId="client_id" :fieldLabel="__('app.client')" fieldRequired="true">
</x-forms.label>

<x-forms.input-group>
    <select class="form-control select-picker" data-live-search="true" data-size="8"
        name="client_id" id="client_id">
        <option value="">--</option>
        @foreach ($clients as $clientOpt)
            <option 
                @if (!is_null($selected) && $selected == $clientOpt->id)
                    selected
                @endif
                data-content="<x-client-search-option :user='$clientOpt' />"
                value="{{ $clientOpt->id }}">{{ ucwords($clientOpt->name) }} </option>
        @endforeach
    </select>

    @if ($addClientPermission == 'all' || $addClientPermission == 'added')
        <x-slot name="append">
            <a href="{{ route('clients.create') }}" id="add-client"
                class="btn btn-outline-secondary border-grey openRightModal"
                data-redirect-url="{{ url()->full() }}">@lang('app.add')</a>
        </x-slot>
    @endif
</x-forms.input-group>