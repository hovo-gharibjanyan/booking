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

        if ($booking) {
            $booking->load('tour');
            $pdf = Pdf::loadView('pdf.voucher', ['booking' => $booking]);
            $pdfData = $pdf->output();
            Mail::to($booking->customer_email)->send(new BookingVoucherMail($booking, $pdfData));
        }

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
}