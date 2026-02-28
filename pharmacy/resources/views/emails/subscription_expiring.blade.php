<p>Dear {{ $company->name }},</p>
<p>Your subscription will expire on <strong>{{ $company->subscriptions->last()->end_date }}</strong>.</p>
<p>Please renew to avoid interruption of service.</p>
