<x-cards.notification :notification="$notification"  :link="route('creditnotes.show', md5($notification->data['id']))" :image="$global->logo_url"
    :title="__('email.creditNote.subject')" :link="$notification->data['cn_number']"
    :time="$notification->created_at" />
