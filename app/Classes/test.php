<?php

/**
 * Created by PhpStorm.
 * User: ShaOn
 * Date: 11/29/2018
 * Time: 12:49 AM
 */

namespace App\Classes;

use App\{
    Models\EmailTemplate,
    Models\Generalsetting
};
use App\Models\Order;
use DB;
use Config;
use Exception;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PDF;
use Illuminate\Support\Str;

class GeniusMailer
{
    public $owner;
    public function __construct()
    {
        $this->gs = Generalsetting::findOrFail(1);

        $this->mail = new PHPMailer(true);

        if ($this->gs->is_smtp == 1) {

            $this->mail->isSMTP();                          // Send using SMTP
            $this->mail->Host       = $this->gs->mail_host;       // Set the SMTP server to send through
            $this->mail->SMTPAuth   = true;                 // Enable SMTP authentication
            $this->mail->Username   = $this->gs->mail_user;   // SMTP username
            $this->mail->Password   = $this->gs->mail_pass;   // SMTP password
            $this->mail->SMTPSecure = $this->gs->mail_encryption;      // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $this->mail->Port       = $this->gs->mail_port;
        }
    }

    public function sendAutoOrderMail(array $mailData, $id)
    {
        $temp = EmailTemplate::where('email_type', '=', $mailData['type'])->first();
        $order = Order::findOrFail($id);
        $cart = json_decode($order->cart, true);
        try {

            $body = preg_replace("/{customer_name}/", $mailData['cname'], $temp->email_body);
            $body = preg_replace("/{order_amount}/", $mailData['oamount'], $body);
            $body = preg_replace("/{admin_name}/", $mailData['aname'], $body);
            $body = preg_replace("/{admin_email}/", $mailData['aemail'], $body);
            $body = preg_replace("/{order_number}/", $mailData['onumber'], $body);
            $body = preg_replace("/{website_title}/", $this->gs->title, $body);


            $fileName = public_path('assets/temp_files/') . Str::random(4) . time() . '.pdf';
            $pdf = PDF::loadView('pdf.order', compact('order', 'cart'))->save($fileName);

            //Recipients
            $this->mail->setFrom($this->gs->from_email, $this->gs->from_name);
            $this->mail->addAddress($mailData['to']);     // Add a recipient

            // Attachments
            $this->mail->addAttachment($fileName);

            // Content
            $this->mail->isHTML(true);

            $this->mail->Subject = $temp->email_subject;

            $this->mail->Body = $body;

            $this->mail->send();
        } catch (Exception $e) {
        }

        $files = glob('assets/temp_files/*'); //get all file names
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file); //delete file
        }

        return true;
    }


    public function sendAutoMail(array $mailData)
    {

        if (!empty($this->owner)) {
            $setup = Generalsetting::whereRegisterId($this->owner->id)->first();

            $temp = EmailTemplate::whereRegisterId($this->owner->id)->where('email_type', '=', $mailData['type'])->first();
        } else {
            $setup = Generalsetting::find(1);

            $temp = EmailTemplate::whereRegisterId(0)->where('email_type', '=', $mailData['type'])->first();
        }


        $body = preg_replace("/{customer_name}/", $mailData['cname'], $temp->email_body);
        $body = preg_replace("/{order_amount}/", $mailData['oamount'], $body);
        $body = preg_replace("/{admin_name}/", $mailData['aname'], $body);
        $body = preg_replace("/{admin_email}/", $mailData['aemail'], $body);
        $body = preg_replace("/{order_number}/", $mailData['onumber'], $body);
        $body = preg_replace("/{website_title}/", $setup->title, $body);

        $data = [
            'email_body' => $body
        ];

        if ($setup->is_smtp == 1) {

            $objDemo = new \stdClass();
            $objDemo->to = $mailData['to'];
            $objDemo->from = $setup->from_email;
            $objDemo->title = $setup->from_name;
            $objDemo->subject = $temp->email_subject;


            Mail::send('admin.email.mailbody', $data, function ($message) use ($objDemo) {
                $message->from($objDemo->from, $objDemo->title);
                $message->to($objDemo->to);
                $message->subject($objDemo->subject);
            });
        } else {
            $to = $mailData['to'];
            $subject = $temp->email_subject;
            $from = $setup->from_email;

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Create email headers
            $headers .= 'From: ' . $from . "\r\n" .
                'Reply-To: ' . $from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            // Sending email
            mail($to, $subject, $data['email_body'], $headers);
        }
    }

    public function sendCustomMail(array $mailData)
    {
        if (!empty($this->owner)) {
            $setup = Generalsetting::whereRegisterId($this->owner->id)->first();
        } else {
            $setup = Generalsetting::find(1);
        }
        $data = ['email_body' => $mailData['body']];

        if ($setup->is_smtp == 1) {

            $objDemo = new \stdClass();
            $objDemo->to = $mailData['to'];
            $objDemo->from = $setup->from_email;
            $objDemo->title = $setup->from_name;
            $objDemo->subject = $mailData['subject'];


            try {
                Mail::send('admin.email.mailbody', $data, function ($message) use ($objDemo) {
                    $message->from($objDemo->from, $objDemo->title);
                    $message->to($objDemo->to);
                    $message->subject($objDemo->subject);
                });
            } catch (\Exception $e) {
                //die("Not sent");
            }
        } else {
            $to = $mailData['to'];
            $subject = $mailData['subject'];
            $from = $setup->from_email;

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Create email headers
            $headers .= 'From: ' . $from . "\r\n" .
                'Reply-To: ' . $from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            // Sending email
            mail($to, $subject, $data['email_body'], $headers);
        }

        return true;
    }
}
