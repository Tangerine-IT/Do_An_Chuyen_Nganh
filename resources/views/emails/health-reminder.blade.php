@component('mail::message')
# {{ config('petshop.name') }}

{{ __('mail.health_reminder_greeting', ['name' => $notifiable->name]) }}

{{ __('mail.health_reminder_line', [
    'pet' => $record->pet->name,
    'type' => $record->type->label(),
    'date' => $record->next_due_date->format('d/m/Y'),
]) }}

@component('mail::button', ['url' => route('services.index', ['pet' => $record->pet_id)])
{{ __('mail.health_reminder_action') }}
@endcomponent

{{ __('mail.health_reminder_footer') }}

{{ config('petshop.name') }}
@endcomponent
