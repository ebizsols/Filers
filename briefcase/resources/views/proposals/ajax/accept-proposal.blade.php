<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.estimates.signatureAndConfirmation')</h5>
    <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <x-form id="acceptEstimate">
        <input type="hidden" name="type" value="accept" id="action_type">
        <div class="row">
            <div class="col-md-6">
                <x-forms.text fieldId="full_name" :fieldLabel="__('app.name')" fieldName="full_name" fieldRequired="true">
                </x-forms.text>
            </div>

            <div class="col-md-6">
                <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email" fieldRequired="true">
                </x-forms.text>
            </div>
            <div class="col-sm-12 bg-grey p-4">
                <x-forms.label fieldId="signature-pad" :fieldLabel="__('modules.estimates.signature')" />
                <div class="signature_wrap wrapper bg-grey form-control">
                    <canvas id="signature-pad" class="signature-pad rounded" width=400 height=150></canvas>
                </div>
            </div>
            <div class="col-sm-12 mt-3">
                <x-forms.button-secondary id="undo-signature">@lang('modules.estimates.undo')</x-forms.button-secondary>
                <x-forms.button-secondary class="ml-2" id="clear-signature">@lang('modules.estimates.clear')
                </x-forms.button-secondary>
            </div>

        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-signature" icon="check">@lang('app.sign')</x-forms.button-primary>
</div>
