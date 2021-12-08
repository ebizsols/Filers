<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.estimates.signatureAndConfirmation')</h5>
    <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <x-form id="acceptEstimate">
        <div class="row">
            <div class="col-md-4">
                <x-forms.text fieldId="first_name" :fieldLabel="__('modules.estimates.firstName')"
                    fieldName="first_name" fieldRequired="true">
                </x-forms.text>
            </div>
            <div class="col-md-4">
                <x-forms.text fieldId="last_name" :fieldLabel="__('modules.estimates.lastName')" fieldName="last_name"
                    fieldRequired="true">
                </x-forms.text>
            </div>
            <div class="col-md-4">
                <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email" fieldRequired="true">
                </x-forms.text>
            </div>
            <div class="col-sm-12 bg-grey p-4">
                <x-forms.label fieldId="signature-pad" fieldRequired="true" :fieldLabel="__('modules.estimates.signature')" />
                <div class="signature_wrap wrapper border-0 form-control">
                    <canvas id="signature-pad" class="signature-pad rounded" width=400 height=150></canvas>
                </div>
            </div>
            <div class="col-sm-12 mt-3">
                <x-forms.button-secondary id="undo-signature">@lang('modules.estimates.undo')</x-forms.button-secondary>
                <x-forms.button-secondary class="ml-2" id="clear-signature">@lang('modules.estimates.clear')</x-forms.button-secondary>
            </div>

        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-signature" icon="check">@lang('app.sign')</x-forms.button-primary>
</div>

