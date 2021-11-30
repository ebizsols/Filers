<x-cards.notification :notification="$notification"  :link="route('payments.show', $notification->data['id'])" :image="$global->logo_url"
    :title="__('email.invoices.paymentReceived')" :time="$notification->created_at" />
