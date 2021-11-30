<x-cards.notification :notification="$notification"  link="javascript:;" :image="$global->logo_url"
    :title="__('app.welcome') . ' ' . __('app.to') . ' ' . $companyName . ' !'" :time="$notification->created_at" />
