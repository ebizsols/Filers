<x-cards.notification :notification="$notification"  :link="route('tickets.show', $notification->data['id'])" :image="user()->image_url"
    :title="__('email.noticeUpdate.subject')" :link="ucfirst($notification->data['heading'])"
    :time="$notification->created_at" />
