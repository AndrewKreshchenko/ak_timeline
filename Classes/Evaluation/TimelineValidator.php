<?php

declare(strict_types=1);

/*
 * This file is part of the package ak/ak-timelinevis.
 */

namespace AK\TimelineVis\Evaluation;

use \AK\TimelineVis\Domain\Model\Timeline;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;

/*
 * This class checks, if given time entry like 08:34 is valid in TCA.
 */
class TimelineValidator
{
    /**
     * Helper method. Get timeline by specified relation
     */
    private function retrieveRangeStart(Timeline $timeline)
    {
        return $timeline->getRangeStart();
    }

    /**
     * JavaScript code for client side validation/evaluation
     *
     * @return string JavaScript code for client side validation/evaluation
     */
    public function returnFieldJS() 
    {
        return 'return value;';
    }

    /**
     * Server-side validation/evaluation on saving the record
     *
     * @param string $value The field value to be evaluated
     * @param string $is_in The "is_in" value of the field configuration from TCA
     * @param bool $set Boolean defining if the value is written to the database or not.
     * @return string Evaluated field value
     */
    public function evaluateFieldValue($value, $is_in, &$set)
    {
        $formData = GeneralUtility::_GP('data');
        $timelineId = key($formData['tx_timelinevis_domain_model_timeline']);
        $timeline = $formData['tx_timelinevis_domain_model_timeline'][$timelineId];

        $timelineStart = new \DateTime($timeline['range_start']);
        $valueStart = $timelineStart->format('Y-m-d');
        $timelineEnd = new \DateTime($timeline['range_end']);
        $valueEnd = $timelineEnd->format('Y-m-d');

        if ($timelineStart > $timelineEnd) {
            if (is_null($value) || !strlen($value)) {
                return $value;
            }

            $queryImage = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_timelinevis_domain_model_timeline');
            $queryArray = $queryImage
            ->select('tx_timelinevis_domain_model_timeline' . '.uid','title','range_start','range_end')
            ->where(
                $queryImage->expr()->in('uid', $timelineId)
            )
            ->from('tx_timelinevis_domain_model_timeline')
            ->execute()->fetchAll();

            $dbDateStart = \DateTime::createFromFormat('Y-m-d', $queryArray[0]['range_start']);
            $dbValueStart = $dbDateStart->getTimestamp();
            $dbDateEnd = \DateTime::createFromFormat('Y-m-d', $queryArray[0]['range_end']);
            $dbValueEnd = $dbDateEnd->getTimestamp();

            if ($value == $timelineStart->getTimestamp()) {
                $this->flashMessage('Invalid field value in timeline "' . $queryArray[0]['title'] . '"', 'End date can not be before timeline start date.');

                return $dbValueStart;
            } else if ($value == $timelineEnd->getTimestamp()) {
                return $dbValueEnd;
            }

            return $value;
        }

        return $value;
    }

    /**
     * @param string $messageTitle
     * @param string $messageText
     * @param int $severity
     */
    protected function flashMessage($messageTitle, $messageText, $severity = FlashMessage::ERROR)
    {
        // TODO make with LocalizationUtility

        // show messages in TYPO3 BE when started manually
        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $messageText, $messageTitle, $severity, true);
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($flashMessage);
    }
}

