<x-cards.notification :notification="$notification"  :link="route('tasks.show', $notification->data['id']) . '?view=sub_task'"
    :image="user()->image_url" :title="__('email.subTaskCreated')" 
    :time="$notification->created_at" />
