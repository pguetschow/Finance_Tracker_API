<?php
declare(strict_types=1);


namespace App\Mailer;


use App\Entity\User;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class MailHandler implements MailHandlerInterface
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $render;

    /**
     * @var string
     */
    private $senderAddress;

    /**
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $render
     * @param string $senderAddress
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $render, string $senderAddress)
    {
        $this->mailer = $mailer;
        $this->render = $render;
        $this->senderAddress = $senderAddress;
    }


    /**
     * @param User $user
     * @throws \Exception
     */
    public function resetPasswordMail(User $user): void
    {
        $message = (new \Swift_Message('Passwort zurücksetzen'))
            ->setTo($user->getEmail())
            ->setFrom([$this->senderAddress])
            ->setBody(
                $this->render->render(
                    'mail/password_reset.html.twig',
                    [
                        'user' => $user,
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);

    }

    /**
     * @param User $user
     * @param string $output
     * @throws \Exception
     */
    public function gdprExportMail(User $user, string $output): void
    {
        $message = (new \Swift_Message('DSGVO Export'))
            ->setTo($user->getEmail())
            ->setFrom([$this->senderAddress])
            ->setBody(
                'Anbei finden Sie die von Ihnen angeforderten Daten gemäß der DSGVO Absatz 13.'
            );

        $message->attach(new \Swift_Attachment($output, 'export.pdf', 'application/pdf'));

        $this->mailer->send($message);
    }


}
