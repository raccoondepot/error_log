<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Model\Error;
use RD\ErrorLog\Domain\Model\Filter;
use RD\ErrorLog\Domain\Enum\Frequency;
use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Repository\ErrorRepository;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class ReportService
{
    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
        private readonly ErrorRepository $errorRepository
    ) {
    }

    public function run(): void
    {
        if (date('i') == 0) {
            $this->dispatchReport(new Frequency(Frequency::HOURLY));

            if (date('G') == 0) {
                $this->dispatchReport(new Frequency(Frequency::DAILY));

                if (date('w') == 1) {
                    $this->dispatchReport(new Frequency(Frequency::WEEKLY));
                }

                if (date('d') == 1) {
                    $this->dispatchReport(new Frequency(Frequency::MONTHLY));
                }
            }
        }
        $this->notifyAboutNotDispatchedErrors();
    }

    private function notifyAboutNotDispatchedErrors(): void
    {
        $filter = new Filter();
        $filter->setEventDispatched(false);
        $errors = $this->errorRepository->getErrors($filter, false);
        if ($errors === []) {
            return;
        }

        $groupedErrors = [];
        foreach ($errors as $error) {
            $errorTypeHash = $this->errorRepository->generateErrorTypeHash($error);
            if (!isset($groupedErrors[$errorTypeHash])) {
                $groupedErrors[$errorTypeHash] = [];
            }
            $groupedErrors[$errorTypeHash] = $error;
        }
        foreach ($groupedErrors as $errorTypeHash => $error) {
            $isFirstOccurrence = $this->errorRepository->isFirstOccurrence($errorTypeHash);
            if ($isFirstOccurrence) {
                $this->errorRepository->createOccurrence($errorTypeHash, $error['uid']);
            }
            $errorObject = new Error($error);
            $this->eventDispatcher->dispatch(new ErrorEvent($errorObject, $isFirstOccurrence));
        }
        $this->errorRepository->setDispatchedEventForErrors(array_column($errors, 'uid'));
    }

    private function dispatchReport(Frequency $frequency)
    {
        $errors = $this->getErrors($frequency->seconds());
        $this->eventDispatcher->dispatch(new ReportEvent($frequency, $errors));
    }

    private function getErrors(int $startTime, int $endTime = null): array
    {
        $filter = new Filter();
        $filter->setStart(date('Y-m-d H:i:s', $startTime));
        $filter->setEnd(date('Y-m-d H:i:s', $endTime ?? time()));

        return $this->errorRepository->getErrors($filter);
    }
}
