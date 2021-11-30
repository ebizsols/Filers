<x-cards.notification :notification="$notification"  :link="route('notices.show', $notification->data['id'])" :image="$global->logo_url"
    :title="__('email.newNotice.subject')"  :time="$notification->created_at" />
