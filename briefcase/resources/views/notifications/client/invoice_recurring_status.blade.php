<x-cards.notification :notification="$notification"  :link="route('recurring-invoices.show', $notification->data['id'])" :image="$global->logo_url"
    :title="__('email.invoiceRecurringStatus.subject')" :link="$notification->data['event_name']"
    :time="$notification->created_at" />
