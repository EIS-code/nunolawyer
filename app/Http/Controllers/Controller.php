<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;
use App\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ExceptionOccured;
use Carbon\Carbon;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SerializesModels;

    public function sendMail($view, $to, $subject, $body, $toName = '', $cc = '', $bcc = '', $attachments = [])
    {
    	if (empty($view)) {
            return response()->json([
                'code' => 401,
                'msg'  => __('Please provide email view.')
            ]);
        }

        $validator = Email::validator(['to' => $to, 'subject' => $subject, 'body' => $body]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 401,
                'msg'  => $validator->errors()->first()
            ]);
        }

        $bodyContent = View::make('app.emails.'. $view, compact('body'))->render();
        Mail::send('app.emails.'. $view, compact('body'), function($message) use ($to, $subject, $toName, $cc, $bcc, $attachments) {
            $message->to($to, $toName)
                    ->subject($subject);
            if (!empty($cc)) {
                $message->cc($cc);
            }

            if (!empty($bcc)) {
                $message->bcc($bcc);
            }

            if (!empty($attachments)) {
            	foreach ($attachments as $attachment) {
            		if (empty($attachment['path'])) {
            			continue;
            		}

            		$as   = (!empty($attachment['as'])) ? $attachment['as'] : '';
            		$mime = (!empty($attachment['mime'])) ? $attachment['mime'] : '';

            		$message->attach($attachment['path'], ['as' => $as, 'mime' => $mime]);
            	}
            }
        });

        if (Mail::failures()) {
            return response()->json([
                'code' => 401,
                'msg'  => __('Email not sent')
            ]);
        } else {
        	foreach ($to as $mailId) {
	            Email::insert([
	                'from'           => env('MAIL_FROM_ADDRESS', ''),
	                'to'             => $toName . ' ' . $mailId,
	                'cc'             => $cc,
	                'bcc'            => $bcc,
	                'subject'        => $subject,
	                'body'           => $bodyContent,
	                'attachments'	 => json_encode($attachments),
	                'exception_info' => NULL,
	                'created_at'     => Carbon::now()
	            ]);
            }

            return response()->json([
                'code' => 200,
                'msg'  => __('Email sent successfully !')
            ]);
        }
    }

    public function getJsonResponseCode($response)
    {
        if (!empty($response) && $response instanceof \Illuminate\Http\JsonResponse) {
            return (!empty($response->getData()->code)) ? $response->getData()->code : false;
        }

        return false;
    }

    public function getJsonResponseMsg($response)
    {
        if (!empty($response) && $response instanceof \Illuminate\Http\JsonResponse) {
            return (!empty($response->getData()->msg)) ? $response->getData()->msg : false;
        }

        return false;
    }
}
