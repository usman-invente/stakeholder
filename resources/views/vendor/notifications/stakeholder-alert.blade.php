@component('mail::message')
# Stakeholder Communication Alert

There are {{ $count }} stakeholders without communication in the last {{ $threshold }} days.

The following stakeholders require attention:

{!! $tableHtml !!}

@component('mail::button', ['url' => $actionUrl])
{{ $actionText }}
@endcomponent

Please review and take necessary action.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
