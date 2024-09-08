<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\VehicleJob;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class JobCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $vehicleJob;
    public $customer;

    public function __construct(VehicleJob $vehicleJob, Customer $customer)
    {
        $this->vehicleJob = $vehicleJob;
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject('Your Vehicle Job has been Completed!')
            ->view('emails.job-completed')
            ->with([
                'vehicleJob' => $this->vehicleJob,
                'customer' => $this->customer,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Completed Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job-completed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
