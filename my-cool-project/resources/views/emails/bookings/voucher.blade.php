<x-mail::message>
# Подтверждение бронирования
Здравствуйте, **{{ $booking->customer_name }}**!
Ваш ваучер на тур "{{ $booking->tour->title }}" прикреплен к этому письму.
Спасибо,<br>
{{ config('app.name') }}
</x-mail::message>