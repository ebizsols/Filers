@extends('layouts.admin')

@section('title', trans('quickbooks::general.name'))

@section('content')
    @include('quickbooks::double_entry', ['double_entry_enabled' => $double_entry_enabled])

    {!! Form::open([
        'method' => 'POST',
        'route' => 'quickbooks.settings.update',
        'id'     => 'quickbooks',
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
                                {{ Form::textGroup('client_id', trans('quickbooks::general.form.client_id'), 'fas fa-barcode', ['required' => 'required'], old('client_id', setting('quickbooks.client_id'))) }}

                                {{ Form::textGroup('client_secret', trans('quickbooks::general.form.client_secret'), 'fas fa-key', ['required' => 'required'], old('client_secret', setting('quickbooks.client_secret'))) }}

{{--                                {{ Form::invoice_text('environment', trans('quickbooks::general.form.environment'), 'font', ['development' => trans('quickbooks::general.form.development'),'production' => trans('quickbooks::general.form.production')], setting('quickbooks.environment'), [], 'environment', setting('quickbooks.environment')) }}--}}
                            @else
                                Please click into Auth button and connect your Quickbooks company with Akaunting application to can access to your data and have option to copy it to Akaunting application.
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="col-md-12">
                            <div class="row float-right">
                                @if (!$is_cloud)
                                    <div> {{ Form::saveButtons('settings.index') }}</div>
                                    @if (!empty(setting('quickbooks.client_id')) && !empty(setting('quickbooks.client_secret')))
                                        <div>
                                            @php session(['quickbooks_company_id' => company_id()]) @endphp
                                            <a class="btn btn-warning items-align-center" href="{{ route('quickbooks.auth.start') }}">
                                                <span class="fas fa-key"></span>&nbsp; {{ trans('quickbooks::general.form.sync.auth') }}
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    @if (empty(setting('quickbooks.realm_id')))
                                        <div>
                                            @php session(['quickbooks_company_id' => company_id()]) @endphp
                                            <a class="btn btn-warning items-align-center" href="{{ route('quickbooks.auth.start') }}">
                                                <span class="fas fa-key"></span>&nbsp; {{ trans('quickbooks::general.form.sync.auth') }}
                                            </a>
                                        </div>
                                    @endif
                                @endif

                                @if (!empty(setting('quickbooks.enabled')))
                                    <div class="dropdown pl-3">
                                        <button type="button" class="btn btn-default items-align-center" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fa fa-chevron-down"></span>&nbsp; {{ trans('quickbooks::general.form.sync.title') }}
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow float left">
                                            @foreach($sync_actions as $type => $action)
                                                @can($action['permission'])
                                                    <button type="button" class="dropdown-item" @click="sync('{{ $type }}')">{{ trans('quickbooks::general.form.sync.' . $type) }}</button>
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
    <script src="{{ asset('modules/Quickbooks/Resources/assets/js/quickbooks.min.js?v=' . module_version('quickbooks')) }}"></script>
@endpush
