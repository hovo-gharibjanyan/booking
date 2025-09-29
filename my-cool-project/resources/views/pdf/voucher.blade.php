<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваучер на бронирование</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Важно для поддержки кириллицы */
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .details-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ваучер на бронирование #{{ $booking->id }}</h1>
        </div>

        <p>Уважаемый(ая) <strong>{{ $booking->customer_name }}</strong>, спасибо за ваш заказ!</p>
        <p>Этот документ подтверждает ваше бронирование. Пожалуйста, сохраните его.</p>

        <table class="details-table">
            <tr>
                <td>Название тура:</td>
                <td>{{ $booking->tour->title }}</td>
            </tr>
            <tr>
                <td>Дата тура:</td>
                <td>{{ $booking->booking_date }}</td>
            </tr>
            <tr>
                <td>Количество мест:</td>
                <td>{{ $booking->seats }}</td>
            </tr>
            <tr>
                <td>Статус:</td>
                <td>{{ $booking->status }}</td>
            </tr>
            <tr>
                <td>Общая стоимость:</td>
                <td>{{ ($booking->total_minor / 100) }} у.е.</td>
            </tr>
        </table>

        <div class="footer">
            <p>BookingLite - Ваши путешествия начинаются здесь!</p>
            <p>Дата выдачи: {{ now()->format('Y-m-d') }}</p>
        </div>
    </div>
</body>
</html>