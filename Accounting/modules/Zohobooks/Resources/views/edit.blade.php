@extends('layouts.admin')

@section('title', trans('zohobooks::general.name'))

@section('content')
    @include('zohobooks::double_entry', ['double_entry_enabled' => $double_entry_enabled])

    {!! Form::open([
        'method' => 'POST',
        'route' => 'zohobooks.settings.update',
        'id'     => 'zohobooks',
        '@submit.prevent' => 'onSubmit',
        '@keydown' => 'form.errors.clear($event.target.name)',
        'files' => true,
        'role' => 'form',
        'class' => 'form-loading-button',
        'novalidate' => true
    ]) !!}

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if (!$is_cloud)
                            {{ Form::textGroup('client_id', trans('zohobooks::general.form.client_id'), 'fas fa-barcode', ['required' => 'required'], old('client_id', setting('zohobooks.client_id'))) }}

                            {{ Form::textGroup('client_secret', trans('zohobooks::general.form.client_secret'), 'fas fa-key', ['required' => 'required'], old('client_secret', setting('zohobooks.client_secret'))) }}

                        @endif
                        {{ Form::textGroup('organization_id', trans('zohobooks::general.form.organization_id'), 'fas fa-building', ['required' => 'required'], old('organization_id', setting('zohobooks.organization_id'))) }}
                    </div>
                </div>

                <div class="card-footer">
                    <div class="col-md-12">
                        <div class="row float-right">
                            <div> {{ Form::saveButtons('settings.index') }}</div>
                            @php session(['zohobooks_company_id' => company_id()]) @endphp
                            <a class="btn btn-warning items-align-center"
                               href="{{ route('zohobooks.auth.start') }}">
                                            <span
                                                class="fas fa-key"></span>&nbsp; {{ trans('zohobooks::general.form.sync.auth') }}
                            </a>
                            @if (!empty(setting('zohobooks.enabled')))
                                <div class="dropdown pl-3">
                                    <button type="button" class="btn btn-default items-align-center" role="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span
                                            class="fa fa-chevron-down"></span>&nbsp; {{ trans('zohobooks::general.form.sync.title') }}
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow float left">
                                        @foreach($sync_actions as $type => $action)
                                            @can($action['permission'])
                                                <button type="button" class="dropdown-item"
                                                        @click="sync('{{ $type }}')">{{ trans('zohobooks::general.form.sync.' . $type) }}</button>
                                            @endcan
                                            <div class="dropdown-divider"></div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

    <component v-bind:is="sync_html" @sync="sync($event)"></component>
@endsection

@push('scripts_start')
    <script src="{{ asset('modules/ZohoBooks/Resources/assets/js/zohobooks.min.js?v=' . module_version('zohobooks')) }}"></script>
@endpush
