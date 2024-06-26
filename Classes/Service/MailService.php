<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class MailService implements SingletonInterface
{
    private StandaloneView $standaloneView;
    private MailMessage $mailMessage;

    public function __construct(StandaloneView $standaloneView, MailMessage $mailMessage)
    {
        $this->standaloneView = $standaloneView;
        $this->mailMessage = $mailMessage;
    }

    public function sendEmail(string $template, BackendUser $user, $variables, $mailSubject): void
    {
        if (empty($user->getEmail())) {
            return;
        }

        $this->standaloneView->setTemplatePathAndFilename($template);
        $this->standaloneView->assignMultiple([
            'name' => $user->getRealName() ?? $user->getUserName(),
            'errors' => $variables
        ]);

        $emailBody = $this->standaloneView->render();

        $this->mailMessage
            ->to(new Address($user->getEmail(), $user->getRealName() ?? $user->getUsername()))
            ->from(new Address($this->getSenderEmailAddress(), $this->getSenderEmailName()))
            ->subject($mailSubject)
            ->html($emailBody);

        $this->mailMessage->send();
    }

    protected function getSenderEmailAddress(): string
    {
        return !empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])
            ? $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']
            : 'no-reply@example.com';
    }

    protected function getSenderEmailName(): string
    {
        return !empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])
            ? $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']
            : 'Report Service';
    }
}
