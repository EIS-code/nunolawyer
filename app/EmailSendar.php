<?php

namespace App;

use Illuminate\Support\Facades\Mail;
use App\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ExceptionOccured;
use Carbon\Carbon;
use View;

class EmailSendar
{
    use Queueable, SerializesModels;

    protected $email;

    public function __construct()
    {
        $this->email = new Email();
    }

    public function send($view, $to, $subject, $body, $toName = '', $cc = '', $bcc = '')
    {
        if (empty($view)) {
            return response()->json([
                'code' => 401,
                'msg'  => 'Please provide email view.'
            ]);
        }

        $validator = $this->email->validator(['to' => $to, 'subject' => $subject, 'body' => $body]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 401,
                'msg'  => $validator->errors()->first()
            ]);
        }

        $bodyContent = View::make('app.emails.'. $view, compact('body'))->render();
        Mail::send('app.emails.'. $view, compact('body'), function($message) use ($to, $subject, $toName, $cc, $bcc) {
            $message->to($to, $toName)
                    ->subject($subject);
            if (!empty($cc)) {
                $message->cc($cc);
            }

            if (!empty($bcc)) {
                $message->bcc($bcc);
            }
        });

        if (Mail::failures()) {
            return response()->json([
                'code' => 401,
                'msg'  => 'Email not sent'
            ]);
        } else {
            $this->insertEmail([
                'from'           => env('MAIL_FROM_ADDRESS', ''),
                'to'             => $toName . ' ' . $to,
                'cc'             => $cc,
                'bcc'            => $bcc,
                'subject'        => $subject,
                'body'           => $bodyContent,
                'exception_info' => NULL,
                'created_at'     => Carbon::now()
            ]);

            return response()->json([
                'code' => 200,
                'msg'  => 'Email sent successfully !'
            ]);
        }
    }

    public function sendException($css, $content)
    {

        $allMails  = config('mail.mailers.exception.emails');
        $mailArray = (!empty($allMails)) ? explode(",", $allMails) : ['it.jaydeep.mor@gmail.com'];
        $subject   = 'Urgent !!! Exception: ' . \Request::fullUrl();

        return $this->send('exception', $mailArray, $subject, ['css' => $css, 'content' => $content]);
    }

    public function insertEmail(array $data)
    {
        return $this->email->insert($data);
    }
}
