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
use \AK\TimelineVis\Timeline\FarDate\FarDate;

use TYPO3\CMS\Core\Log\LogManager;
// @TODO make with LocalizationUtility

/*
 * This class checks, if given time entry like 08:34 is valid in TCA.
 */
class PointValidator
{
    private const ERA_BEGIN = -62167219200;
    private const DAY_TSTAMP = 86400;

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

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        $timelineStartTStamp = 0;

        if (strlen($timeline['range_start']) == 0) {
            // @TODO create method for this case
            $timelineStartTStamp = self::ERA_BEGIN;
        } else {
            $timelineStart = new \DateTime($timeline['range_start']);
            $timelineStartTStamp = $timelineStart->getTimestamp();
        }

        $timelineEnd = new \DateTime(is_string($timeline['range_end']) ? $timeline['range_end'] : 'now');
        $timelineEndTStamp = $timelineEnd->getTimestamp();
        $timelineStartDateBC = (bool)$timeline['date_start_b_c'];
        $timelineEndDateBC = (bool)$timeline['date_end_b_c'];

        $farDateError = false;

        // Case of usual D. C. range
        if (!$timelineStartDateBC) {
            if ($value < $timelineStartTStamp || $value > $timelineEndTStamp) {
                $this->flashMessage('Point date is out of timeline range', 'Your input was ' . $value . ' (timeline with index ' . $timelineId .').');
    
                return $timelineStartTStamp;
            }
    
            return $value;
        }

        // Considering B. C. dates
        if ($timelineEndDateBC) {
            $farDateStart = (new FarDate($timelineStartTStamp, $timelineStartDateBC))->getFarDateTimestamp();
            $farDateEnd = (new FarDate($timelineEndTStamp, $timelineEndDateBC))->getFarDateTimestamp();

            $farDateVObj = new FarDate((int)$value, true);
            $farDateV = $farDateVObj->getFarDateTimestamp();

            // Check with minus one day, allow value be equat to timeline end date
            if (($farDateV < $farDateStart) || ($farDateV > $farDateEnd + self::DAY_TSTAMP)) {
                $farDateError = true;
            }
    
            if ($farDateError) {
                $this->flashMessage('Point date is out of timeline range', 'Your input was ' . $value . ' (timeline with index ' . $timelineId .'). Pay attention on B. C. dates.');
        
                return $timelineStartTStamp;
            }

            $this->flashMessage('One point has date "' . $farDateVObj->logDate() . '"', 'B. C. flag must be checked (for your confidence).', FlashMessage::INFO);

            return $value;
        } else {
            $farDateLimit = (new FarDate(($timelineStartTStamp > $timelineEndTStamp ? $timelineStartTStamp : $timelineEndTStamp), false))->getFarDateTimestamp();

            // Set Far date as positive number
            $farDateVObj = new FarDate((int)$value, false);
            $farDateV = $farDateVObj->getFarDateTimestamp();

            if ((-$farDateV < -abs($farDateLimit)) || ($farDateV > $farDateLimit)) {
                $farDateError = true;
            }
    
            if ($farDateError) {
                $this->flashMessage('Point date is out of timeline range', 'Your input was ' . $value . ' (timeline with index ' . $timelineId .'). Pay attention on B. C. dates.');

                // Save value because it may be right
                return $timelineStartTStamp;
            }

            $this->flashMessage('One point has date "' . $farDateVObj->logDate() . '"', 'You may check if the value may be out of timeline range.', FlashMessage::INFO);

            return $value;
        }
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

