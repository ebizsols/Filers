<x-cards.notification :notification="$notification"  :link="route('invoices.show', $notification->data['id'])" :image="$global->logo_url"
    :title="__('email.invoice.subject')" :link="$notification->data['invoice_number']"
    :time="$notification->created_at" />
