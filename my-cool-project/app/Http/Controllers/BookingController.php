<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Mail\BookingVoucherMail;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia; 

class BookingController extends Controller
{

    public function store(Request $request)
    {
        $user = $request->user();
        $validatedData = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'customer_phone' => 'required|string|max:20',
            'booking_date' => 'required|date',
            'seats' => 'required|integer|min:1',
        ]);
        $tour = Tour::findOrFail($validatedData['tour_id']);

        $booking = null;

        DB::transaction(function () use ($validatedData, $tour, $user, &$booking) {
            $bookedSeats = Booking::where('tour_id', $tour->id)
                ->where('booking_date', $validatedData['booking_date'])
                ->whereIn('status', ['paid', 'reserved'])
                ->sum('seats');

            if (($bookedSeats + $validatedData['seats']) > $tour->max_seats) {
                throw ValidationException::withMessages([
                    'seats' => 'Извините, на эту дату осталось только ' . ($tour->max_seats - $bookedSeats) . ' свободных мест.',
                ]);
            }
            $booking = new Booking();
            $booking->user_id = $user->id;
            $booking->tour_id = $tour->id;
            $booking->customer_name = $user->name;
            $booking->customer_email = $user->email;
            $booking->customer_phone = $validatedData['customer_phone'];
            $booking->booking_date = $validatedData['booking_date'];
            $booking->seats = $validatedData['seats'];
            $booking->total_minor = $tour->price_minor * $validatedData['seats'];
            $booking->status = 'reserved';
            $booking->save();


        });



        return redirect()->route('tours.show', ['tour' => $tour->id])
            ->with('success', 'Тур успешно забронирован! Подтверждение отправлено на ваш email.');
    }

    public function downloadVoucher(Booking $booking)
    {
        if (auth()->id() !== $booking->user_id) {
            abort(403); 
        }

        $pdf = Pdf::loadView('pdf.voucher', ['booking' => $booking]);

        return $pdf->download('voucher-booking-'.$booking->id.'.pdf');
    }

    public function cancel(Booking $booking)
    {
        if (auth()->id() !== $booking->user_id) {
            abort(403);
        }
        if ($booking->status !== 'reserved') {
            return back()->with('error', 'Эту бронь уже нельзя отменить.');
        }
        $booking->status = 'cancelled';
        $booking->save();

        return back()->with('success', 'Бронь успешно отменена.');
    }

    public function showPaymentForm(Booking $booking)
    {
        if (auth()->id() !== $booking->user_id) {
            abort(403); 
        }
        if ($booking->status !== 'reserved') {
            return back()->with('error', 'Эту бронь уже нельзя оплатить.');
        }
        return Inertia::render('Bookings/Payment', [
            'booking' => $booking->load('tour'), 
        ]);
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $request->validate(['payment_token' => 'required']);

        if (auth()->id() !== $booking->user_id || $booking->status !== 'reserved') {
            abort(403);
        }

        // $booking->status = 'pending_confirmation';
        // $booking->save();

        $booking->transactions()->create([
            'amount_minor' => $booking->total_minor,
            'status' => 'pending',
        ]);

        return to_route('dashboard')->with('success', 'Спасибо! Ваш платеж принят в обработку. Статус брони обновится после подтверждения администратором.');
    }

    public function cancelBooking(Booking $booking)
    {
        if (auth()->id() !== $booking->user_id) {
            abort(403);
        }

        // Запрещаем отменять уже отмененные брони
        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Эта бронь уже отменена.');
        }

        $refundAmount = 0; // Сумма к возврату по умолчанию
        $message = 'Бронь успешно отменена.'; // Сообщение по умолчанию

        // Если бронь была оплачена, применяем логику возврата
        if ($booking->status === 'paid') {
            // Считаем, сколько полных дней осталось до тура
            $daysRemaining = now()->startOfDay()->diffInDays($booking->booking_date, false);

            if ($daysRemaining > 2) {
                // Если больше 2 дней, возвращаем 50%
                $refundAmount = $booking->total_minor / 2;
                $message = 'Бронь отменена. Вам будет возвращено 50% стоимости.';
            } else {
                // Если 2 дня или меньше, ничего не возвращаем
                $refundAmount = 0;
                $message = 'Бронь отменена. Возврат средств не предусмотрен, так как до тура осталось менее 2 дней.';
            }
        }
        
        // Обновляем запись в базе
        $booking->status = 'cancelled';
        $booking->refund_amount_minor = $refundAmount;
        $booking->save();

        // Возвращаем пользователя назад с правильным сообщением
        return back()->with('success', $message);
    }
}