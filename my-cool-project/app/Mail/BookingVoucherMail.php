<?php
namespace App\Mail;
use App\Models\Booking; use Illuminate\Bus\Queueable; 
use Illuminate\Mail\Mailable; use Illuminate\Mail\Mailables\Attachment; 
use Illuminate\Mail\Mailables\Content; use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class BookingVoucherMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public Booking $booking, public string $pdfData) {}
    public function envelope(): Envelope { return new Envelope(subject: 'Ваше бронирование подтверждено! Ваучер #' . $this->booking->id); }
    public function content(): Content { return new Content(markdown: 'emails.bookings.voucher'); }
    public function attachments(): array {
        return [ Attachment::fromData(fn () => $this->pdfData, 'voucher.pdf')->withMime('application/pdf') ];
    }
}