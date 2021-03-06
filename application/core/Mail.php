<?php

/**
 * Class Mail
 *
 * Handles everything regarding mail-sending.
 */
class Mail
{
    /** @var mixed variable to collect errors */
    private $error;

    /**
     * Try to send a mail by using PHPMailer.
     * Make sure you have loaded PHPMailer via Composer.
     * Depending on your EMAIL_USE_SMTP setting this will work via SMTP credentials or via native mail()
     *
     * @param string $user_email
     * @param string $from_email
     * @param string $from_name
     * @param string $subject
     * @param string $body
     *
     * @return bool
     * @throws Exception
     * @throws phpmailerException
     */
    public function sendMailWithPHPMailer($user_email, $from_email, $from_name, $subject, $body)
    {
        $mail = new PHPMailer;
        
        // you should use UTF-8 to avoid encoding issues
        $mail->CharSet = 'UTF-8';

        // if you want to send mail via PHPMailer using SMTP credentials
        if (Config::get('EMAIL_USE_SMTP')) {

            // set PHPMailer to use SMTP
            $mail->IsSMTP();

            // 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
            $mail->SMTPDebug = 2;
            echo "Setting debug";
            $mail->Debugoutput = function($str, $level) { Session::add('feedback_debug', "$str"); };

            // enable SMTP authentication
            $mail->SMTPAuth = Config::get('EMAIL_SMTP_AUTH');

            // encryption
            if (Config::get('EMAIL_SMTP_ENCRYPTION')) {
                $mail->SMTPSecure = Config::get('EMAIL_SMTP_ENCRYPTION');
            }

            // set SMTP provider's credentials
            $mail->Host = Config::get('EMAIL_SMTP_HOST');
            $mail->Username = Config::get('EMAIL_SMTP_USERNAME');
            $mail->Password = Config::get('EMAIL_SMTP_PASSWORD');
            $mail->Port = Config::get('EMAIL_SMTP_PORT');

        } else {

            $mail->IsMail();
        }

        // fill mail with data
        $mail->From = $from_email;
        $mail->FromName = $from_name;
        $mail->AddAddress($user_email);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // try to send mail, put result status (true/false into $wasSendingSuccessful)
        // I'm unsure if mail->send really returns true or false every time, tis method in PHPMailer is quite complex
        echo "Sending...";
        $wasSendingSuccessful = $mail->Send();

        if ($wasSendingSuccessful) {
            return true;

        } else {

            // if not successful, copy errors into Mail's error property
            $this->error = $mail->ErrorInfo;
            return false;
        }
    }

    /**
     * The main mail sending method, this simply calls a certain mail sending method depending on which mail provider
     * you've selected in the application's config.
     *
     * @param string $user_email email
     * @param string $from_email sender's email
     * @param string $from_name sender's name
     * @param string $subject subject
     * @param string $body full mail body text
     * @return bool the success status of the according mail sending method
     */
    public function sendMail($user_email, $from_email, $from_name, $subject, $body)
    {
        // returns true if successful, false if not
        return $this->sendMailWithPHPMailer($user_email, $from_email, $from_name, $subject, $body);
    }

    /**
     * The different mail sending methods write errors to the error property $this->error,
     * this method simply returns this error / error array.
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
