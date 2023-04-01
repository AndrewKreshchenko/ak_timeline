<?php

declare(strict_types=1);

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Evaluation;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;

use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Core\Log\LogManager;
// @TODO make with LocalizationUtility

/*
 * This class checks, if given time entry like 08:34 is valid in TCA.
 */
class PointValidator
{
    private const ERA_BEGIN = -62167219200;

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
     * Server-side validation/evaluation before saving record
     *
     * @param string $value The field value to be evaluated
     * @param string $is_in The "is_in" value of the field configuration from TCA
     * @param bool $set Boolean defining if the value is written to the database or not. (NOTE remove if never helps)
     * @return string Evaluated field value
     */
    public function evaluateFieldValue($value, $is_in, &$set)
    {
        $formData = GeneralUtility::_GP('data');
        $timelineId = key($formData['tx_timelinevis_domain_model_timeline']);
        $timeline = $formData['tx_timelinevis_domain_model_timeline'][$timelineId];

        $timelineStart = new \DateTime($timeline['range_start']);
        $timelineStartTStamp = $timelineStart->getTimestamp();
        $timelineEnd = new \DateTime($timeline['range_end']);
        $timelineEndTStamp = $timelineEnd->getTimestamp();
        $timelineStartDateBC = $timeline['date_start_b_c'];
        $timelineEndDateBC = $timeline['date_end_b_c'];

        $warningBCIndex = 0;

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        // Calculate B. C. cases
        if ($timelineStartDateBC) {
            $timelineStartTStamp += self::ERA_BEGIN;
        }

        if ($timelineEndDateBC) {
            $timelineEndTStamp += self::ERA_BEGIN;

            if (!$timelineStartDateBC) {
                $warningBCIndex = 1;
            } else if ($timelineStartTStamp < $timelineEndTStamp) {
                $warningBCIndex = 2;
            }
        }

        // Do final check-in
        // In case of error, retrieve old value from DB and save instead
        $logger->warning($value . ' - point value, TL ID ' . $timelineId . ', timeline data ' . implode($timeline));

        if ($value < $timelineStartTStamp || $value > $timelineEndTStamp) {
            $logger->warning($timelineStartTStamp . ' - timelineStartTStamp, timelineEndTStamp is ' . $timelineEndTStamp);
            $this->flashMessage('Point date is out of timeline range', 'Your input was ' . $value . ' (timeline with index' . $timelineId .'.)');

            return $timelineStartTStamp;
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

