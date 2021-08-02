<?php
namespace App\Services;

use mysql_xdevapi\Exception;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

/**
 * Class EmailService
 * @package App\Services
 */
class EmailService
{
    /** @var string */
    public $emailNorely;

    /** @var string */
    public $emailTest;

    /** @var MailerInterface */
    protected $mailer;

    /**
     * EmailService constructor.
     * @param MailerInterface $mailer
     * @param $emailNorely
     * @param $emailTest
     */
    public function __construct(MailerInterface $mailer, $emailNorely, $emailTest)
    {
        $this->mailer      = $mailer;
        $this->emailNorely = $emailNorely;
        $this->emailTest   = $emailTest;
    }


    /**
     * @param $theme
     * @param $subject
     * @param $email
     * @param array $data
     * @return bool|void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send($theme, $subject, $email, array $data)
    {
        if($_ENV['APP_ENV'] === 'dev') {
            $email = $this->emailTest;
        }

        $email = (new TemplatedEmail())
            ->from(new Address($this->emailNorely))
            ->to($email)
            ->subject($subject)
            ->htmlTemplate($theme)
            ->context($data);

        try {
            if ($_ENV['APP_SEND_EMAIL'] === '1') {
                $this->mailer->send($email);
            }
            return true;
        }catch (\Exception $e) {
            return; false;
        }
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function test():void
    {
        $res = $this->send(
            "Emails/test.html.twig",
            'This is a test post.',
            $this->emailTest, [
                "message" => "This is a test post! Email Submission Check!",
            ]
        );

        dump($res);
    }
}