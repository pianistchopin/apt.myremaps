<?php

namespace App\Mail;

use App\Models\FileService;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FileServiceProcessed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The file service instance.
     *
     * @var FileService
     */

    public $fileService;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = EmailTemplate::where('company_id', $this->fileService->user->company->id)->where('label', 'file-service-processed-email')->first(['subject', 'body']);
        if($emailTemplate){
            $subject = $emailTemplate->subject;
            $body = $emailTemplate->body;

            $body = str_replace('##APP_NAME', $this->fileService->user->company->name, $body);
            $body = str_replace('##APP_LOGO', asset('uploads/logo/'. $this->fileService->user->company->logo), $body);
            $body = str_replace('##LINK', $this->fileService->user->company->domain_link.'/customer/file-service', $body);
            $body = str_replace('##CUSTOMER_NAME', $this->fileService->user->full_name, $body);
            $body = str_replace('##CAR_NAME', $this->fileService->car, $body);

            $this->subject($subject)
                ->view('emails.layout')
                ->with(['body'=>$body]);
        }
    }
}
