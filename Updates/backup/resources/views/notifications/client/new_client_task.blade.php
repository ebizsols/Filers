<x-cards.notification :notification="$notification"  :link="route('front.task-share', [$notification->data['hash']])" :image="user()->image_url"
    :title="__('email.newClientTask.subject')" 
    :time="$notification->created_at" />
