@php
$editPermission = user()->permission('edit_product');
$deletePermission = user()->permission('delete_product');
@endphp
<div id="product-detail-section">
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white  border-bottom-grey text-capitalize justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-10 col-10">
                            <h3 class="heading-h1 mb-3">@lang('app.menu.products') @lang('app.details')</h3>
                        </div>
                        <div class="col-lg-2 col-2 text-right">
                            @if (!in_array('client', user_roles()))
                                <div class="dropdown">
                                    <button
                                        class="btn btn-lg f-14 px-2 py-1 text-dark-grey text-capitalize rounded  dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                        aria-labelledby="dropdownMenuLink" tabindex="0">
                                        @if ($editPermission == 'all' || ($editPermission == 'added' && $event->added_by == user()->id))
                                            <a class="dropdown-item openRightModal"
                                                href="{{ route('products.edit', $product->id) }}">@lang('app.edit')
                                            </a>
                                        @endif

                                        @if ($deletePermission == 'all' || ($deletePermission == 'added' && $event->added_by == user()->id))
                                            <a class="dropdown-item delete-product"
                                                data-product-id="{{ $product->id }}">@lang('app.delete')</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <x-cards.data-row :label="__('app.name')" :value="$product->name ?? '--'" />
                    <x-cards.data-row :label="__('app.price')" :value="$product->price ?? '--'" />
                    <x-cards.data-row :label="__('modules.invoices.tax')" :value="$taxValue" />
                    <x-cards.data-row :label="__('app.hsnSac')" :value="$product->hsn_sac_code ?? '--'" />
                    <x-cards.data-row :label="__('modules.productCategory.productCategory')"
                        :value="$category->category_name ?? '--'" />
                    <x-cards.data-row :label="__('modules.productCategory.productSubCategory')"
                        :value="$subCategory->category_name?? '--'" />

                    @if (!in_array('client', user_roles()))
                        <x-cards.data-row :label="__('app.purchaseAllow')" :value="($product->allow_purchase) ? '<span class=\'badge badge-success\'>'.
                            __('app.yes').' </span>': '<span class=\'badge badge-danger\'>'.
                                __('app.no').' </span>'" />
                    @endif

                    <x-cards.data-row :label="__('app.description')" :value="$product->description ?? '--'"
                        html="true" />
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('body').on('click', '.delete-product', function() {
        var id = $(this).data('product-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('products.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            window.location.href = response.redirectUrl;
                        }
                    }
                });
            }
        });
    });

</script>
