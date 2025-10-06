<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Mail\BookingVoucherMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index()
    {

        $bookings = Booking::with(['tour', 'user', 'transactions'])->latest()->get();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function confirmPayment(Booking $booking)
    {
        // Находим все ожидающие транзакции для этой брони
        $pendingTransactions = $booking->transactions()->where('status', 'pending')->get();

        if ($pendingTransactions->isEmpty()) {
            return back()->with('error', 'Нет платежей для подтверждения.');
        }
        
        DB::transaction(function () use ($booking, $pendingTransactions) {
            // Меняем статус брони на 'paid'
            $booking->status = 'paid';
            $booking->paid_at = now();
            $booking->save();

            // Меняем статус транзакций на 'confirmed'
            foreach ($pendingTransactions as $transaction) {
                $transaction->status = 'confirmed';
                $transaction->save();
            }
        });

        $booking->load(['tour', 'user']);

        $pdf = Pdf::loadView('pdf.voucher', ['booking' => $booking]);
        $pdfData = $pdf->output();

        Mail::to($booking->customer_email)->send(new BookingVoucherMail($booking, $pdfData));

        return back()->with('success', 'Оплата подтверждена и ваучер отправлен клиенту!');
    }
}
