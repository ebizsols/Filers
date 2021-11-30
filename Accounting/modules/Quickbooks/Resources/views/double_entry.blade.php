@if (!$double_entry_enabled)
    <div role="alert" class="alert alert-warning">
        <i class="fas fa-balance-scale"></i> &nbsp;{!! trans('quickbooks::general.double_entry', ['url' => route('apps.app.show', ['alias' => 'double-entry'])]) !!}
    </div>
@endif
