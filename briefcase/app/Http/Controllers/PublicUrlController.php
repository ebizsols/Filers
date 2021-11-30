<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\Admin\Contract\SignRequest;
use App\Http\Requests\EstimateAcceptRequest;
use App\Models\AcceptEstimate;
use App\Models\Contract;
use App\Models\ContractSign;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\InvoiceSetting;
use App\Models\Setting;
use App\Models\UniversalSearch;
use App\Models\User;
use App\Notifications\ContractSigned;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class PublicUrlController extends Controller
{

    /* Contract */
    public function contractView(Request $request, $hash)
    {
        $pageTitle = 'app.menu.contracts';
        $pageIcon = 'fa fa-file';
        $contract = Contract::where('hash', $hash)
            ->with('client', 'contractType', 'signature', 'discussion', 'discussion.user')->withoutGlobalScope('company')
            ->firstOrFail();
        return view('contract', ['contract' => $contract, 'global' => $this->global, 'pageTitle' => $pageTitle, 'pageIcon' => $pageIcon]);
    }

    public function contractSign(SignRequest $request, $id)
    {
        $this->contract = Contract::findOrFail($id);

        $sign = new ContractSign();
        $sign->full_name = $request->first_name . ' ' . $request->last_name;
        $sign->contract_id = $this->contract->id;
        $sign->email = $request->email;

        $image = $request->signature;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(32) . '.' . 'jpg';

        if (!File::exists(public_path('user-uploads/' . 'contract/sign'))) {
            $result = File::makeDirectory(public_path('user-uploads/contract/sign'), 0775, true);
        }

        File::put(public_path() . '/user-uploads/contract/sign/' . $imageName, base64_decode($image));

        $sign->signature = $imageName;
        $sign->save();

        Notification::send(User::allAdmins(), new ContractSigned($this->contract, $sign));


        return Reply::redirect(route('contracts.show', $this->contract->id));
    }

    public function contractDownload($id)
    {
        $contract = Contract::findOrFail($id);
        $global = global_setting();

        $this->invoiceSetting = InvoiceSetting::first();
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $pdf->loadView('admin.contracts.contract-pdf', ['contract' => $contract, 'global' => $global]);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = 'contract-' . $contract->id;

        return $pdf->download($filename . '.pdf');
    }

    public function contractDownloadView($id)
    {
        $contract = Contract::findOrFail($id);
        $pdf = app('dompdf.wrapper');
        $global = global_setting();
        $pdf->getDomPDF()->set_option('enable_php', true);
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $pdf->loadView('admin.contracts.contract-pdf', ['contract' => $contract, 'global' => $global]);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = 'contract-' . $contract->id;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function estimateView($hash)
    {
        $estimate = Estimate::where('hash', $hash)->firstOrFail();
        $pageTitle = $estimate->estimate_number;
        $pageIcon = 'icon-people';

        if ($estimate->discount > 0) {
            if ($estimate->discount_type == 'percent') {
                $discount = (($estimate->discount / 100) * $estimate->sub_total);
            }
            else {
                $discount = $estimate->discount;
            }
        }
        else {
            $discount = 0;
        }

        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $estimate->id)
            ->get();

        foreach ($items as $item) {
            if ($estimate->discount > 0 && $estimate->discount_type == 'percent') {
                $item->amount = $item->amount - (($estimate->discount / 100) * $item->amount);
            }

            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = InvoiceItems::taxbyid($tax)->first();

                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {

                        if ($estimate->calculate_tax == 'after_discount' && $discount > 0) {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($item->amount - ($item->amount / $estimate->sub_total) * $discount) * ($this->tax->rate_percent / 100);
    
                        } else{
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $item->amount * ($this->tax->rate_percent / 100);
                        }
    
                    }
                    else {
                        if ($estimate->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($item->amount - ($item->amount / $estimate->sub_total) * $this->discount) * ($this->tax->rate_percent / 100));
    
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + ($item->amount * ($this->tax->rate_percent / 100));
                        }
                    }
                }
            }
        }

        $taxes = $taxList;
        $this->invoiceSetting = InvoiceSetting::first();
        $settings = cache()->remember(
            'global-setting', 60 * 60 * 24, function () {
                return Setting::first();
            }
        );

        return view('estimate', [
            'estimate' => $estimate,
            'taxes' => $taxes,
            'settings' => $settings,
            'discount' => $discount,
            'setting' => $settings,
            'global' => $this->global,
            'companyName' => $settings->company_name,
            'pageTitle' => $pageTitle,
            'pageIcon' => $pageIcon,
            'invoiceSetting' => $this->invoiceSetting,
        ]);

    }

    public function estimateAccept(EstimateAcceptRequest $request, $id)
    {
        DB::beginTransaction();

        $estimate = Estimate::findOrFail($id);

        $accept = new AcceptEstimate();
        $accept->full_name = $request->first_name . ' ' . $request->last_name;
        $accept->estimate_id = $estimate->id;
        $accept->email = $request->email;

        $image = $request->signature;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(32) . '.' . 'jpg';

        if (!File::exists(public_path('user-uploads/' . 'estimate/accept'))) {
            $result = File::makeDirectory(public_path('user-uploads/estimate/accept'), 0775, true);
        }

        File::put(public_path() . '/user-uploads/estimate/accept/' . $imageName, base64_decode($image));

        $accept->signature = $imageName;
        $accept->save();

        $estimate->status = 'accepted';
        $estimate->save();

        $invoice = new Invoice();

        $invoice->client_id = $estimate->client_id;
        $invoice->issue_date = Carbon::now($this->global->timezone)->format('Y-m-d');
        $invoice->due_date = Carbon::now($this->global->timezone)->addDays(invoice_setting()->due_after)->format('Y-m-d');
        $invoice->sub_total = round($estimate->sub_total, 2);
        $invoice->discount = round($estimate->discount, 2);
        $invoice->discount_type = $estimate->discount_type;
        $invoice->total = round($estimate->total, 2);
        $invoice->currency_id = $estimate->currency_id;
        $invoice->note = $estimate->note;
        $invoice->status = 'unpaid';
        $invoice->estimate_id = $estimate->id;
        $invoice->invoice_number = Invoice::lastInvoiceNumber() + 1;
        $invoice->save();

        /** @phpstan-ignore-next-line */
        foreach ($estimate->items as $key => $item) :

            if (!is_null($item)) {
                InvoiceItems::create(
                    [
                        'invoice_id' => $invoice->id,
                        'item_name' => $item->item_name,
                        'item_summary' => $item->item_summary ? $item->item_summary : '',
                        'type' => 'item',
                        'quantity' => $item->quantity,
                        'unit_price' => round($item->unit_price, 2),
                        'amount' => round($item->amount, 2),
                        'taxes' => $item->taxes
                    ]
                );
            }

        endforeach;

        // Log search
        $this->logSearchEntry($invoice->id, $invoice->invoice_number, 'invoices.show', 'invoice');

        DB::commit();

        return Reply::success('Estimate successfully accepted.');
    }

    public function estimateDecline(Request $request, $id)
    {
        $estimate = Estimate::findOrFail($id);
        $estimate->status = 'declined';
        $estimate->save();

        return Reply::dataOnly(['status' => 'success']);
    }

    public function estimateDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->estimate = Estimate::findOrFail($id);
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $this->viewPermission = user()->permission('view_estimates');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->estimate->added_by == user()->id)));

        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');

    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $this->estimate = Estimate::findOrFail($id);

        if ($this->estimate->discount > 0) {

            if ($this->estimate->discount_type == 'percent') {
                $this->discount = (($this->estimate->discount / 100) * $this->estimate->sub_total);
            }
            else {
                $this->discount = $this->estimate->discount;
            }
        }
        else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $this->estimate->id)
            ->get();
        $this->invoiceSetting = invoice_setting();

        foreach ($items as $item) {

            if ($this->estimate->discount > 0 && $this->estimate->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->estimate->discount / 100) * $item->amount);
            }

            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();

                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {

                        if ($this->estimate->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->estimate->sub_total) * $this->discount) * ($this->tax->rate_percent / 100);
    
                        } else{
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $item->amount * ($this->tax->rate_percent / 100);
                        }
    
                    }
                    else {
                        if ($this->estimate->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->estimate->sub_total) * $this->discount) * ($this->tax->rate_percent / 100));
    
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + ($item->amount * ($this->tax->rate_percent / 100));
                        }
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = global_setting();

        $this->invoiceSetting = invoice_setting();

        $pdf = app('dompdf.wrapper');

        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('estimates.pdf.invoice-5', $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));
        $filename = $this->estimate->estimate_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function logSearchEntry($searchableId, $title, $route, $type)
    {
        $search = new UniversalSearch();
        $search->searchable_id = $searchableId;
        $search->title = $title;
        $search->route_name = $route;
        $search->module_type = $type;
        $search->save();

    }

}
