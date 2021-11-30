<x-cards.notification :notification="$notification"  :link="route('contracts.show', md5($notification->data['id']))" :image="$global->logo_url"
    :title="__('email.newContract.subject')" :link="$notification->data['subject']" :time="$notification->created_at" />
