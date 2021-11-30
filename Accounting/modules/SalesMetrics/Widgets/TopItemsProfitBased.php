<?php

namespace Modules\SalesMetrics\Widgets;

use App\Abstracts\Widget;
use App\Models\Document\Document;

class TopItemsProfitBased extends Widget
{
    public $default_name = 'sales-metrics::general.widgets.top_items_profit_based';

    /**
     * Calculation logic consists only invoices.
     */
    public function show()
    {
        $items = collect();

        // Calculation of Invoice items.
        $this->applyFilters(Document::invoice()->with('items.item')->paid(), ['date_field' => 'issued_at'])
            ->each(function ($invoice) use ($items) {
                $invoice->items->each(function ($invoice_item) use ($items) {
                    if ($items->has($invoice_item->item_id)) {
                        $items->get($invoice_item->item_id)->put('amount', $items->get($invoice_item->item_id)->get('amount') + ($invoice_item->total - $invoice_item->quantity * $invoice_item->item->purchase_price));

                        return;
                    }

                    $items->put($invoice_item->item_id, collect([
                        'item_id' => $invoice_item->item_id,
                        'item_name' => $invoice_item->name,
                        'amount' => $invoice_item->total - $invoice_item->quantity * $invoice_item->item->purchase_price
                    ]));
                });

            });

        return $this->view('sales-metrics::top_items_revenue_based', [
            'items' => $items->sortByDesc('amount')->take(5),
        ]);
    }
}
