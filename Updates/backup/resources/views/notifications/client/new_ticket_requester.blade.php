@php
if (!isset($notification->data['subject'])) {
    $ticketReply = \App\TicketReply::with('ticket')->find($notification->data['id']);
    $subject = $ticketReply->ticket->subject;
} else {
    $subject = $notification->data['subject'];
}
$notificationUser = \App\Models\Project::findOrFail($notification->data['user_id']);
@endphp

<x-cards.notification :notification="$notification"  :link="route('tickets.show', $notification->data['id'])" :image="$notificationUser->image_url"
    :title="__('email.ticketReply.subject') . ' #' . $notification->data['id']" :link="$subject"
    :time="$notification->created_at" />
