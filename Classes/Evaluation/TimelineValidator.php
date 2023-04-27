<?php

declare(strict_types=1);

/*
 * Class checks entered dates of Timeline range
 * 
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Evaluation;

use \AK\TimelineVis\Domain\Model\Timeline;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use \AK\TimelineVis\Timeline\FarDate\FarDate;
use TYPO3\CMS\Core\Localization\LanguageService;

class TimelineValidator
{
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
     * Server-side validation/evaluation on saving the record
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

        if (strlen($timeline['range_start']) == 0) {
            return $value;
        } else if (strlen($timeline['range_start']) > 0 && strlen($timeline['range_end']) == 0) {
            return $value;
        }

        $timelineStart = new \DateTime($timeline['range_start']);
        $timelineStartTStamp = $timelineStart->getTimestamp();
        $timelineEnd = new \DateTime(is_string($timeline['range_end']) ? $timeline['range_end'] : 'now');
        $timelineEndTStamp = $timelineEnd->getTimestamp();
        $timelineStartDateBC = (bool)$timeline['date_start_b_c'];
        $timelineEndDateBC = (bool)$timeline['date_end_b_c'];

        $farDateErrorIndex = 0;

        $locale = $this->getFileLocale();

        // B. C. cases
        if ($timelineStartDateBC) {
            $farDateStart = (new FarDate($timelineStartTStamp, $timelineStartDateBC))->getFarDateTimestamp();
            $farDateEnd = (new FarDate($timelineEndTStamp, $timelineEndDateBC))->getFarDateTimestamp();

            $farDateVObj = new FarDate((int)$value, true);
            $farDateV = $farDateVObj->getFarDateTimestamp();

            if (($farDateV < $farDateStart) || ($farDateV > $farDateEnd + self::DAY_TSTAMP)) {
                $farDateErrorIndex = 2;
            }
        } else if ($timelineEndDateBC) {
            $farDateErrorIndex = 1;
        }

        // A. D. cases (final check-in)
        // In case of error, retrieve old value from DB and save instead
        if ($farDateErrorIndex > 0 || ($timelineStartTStamp > $timelineEndTStamp && !$timelineStartDateBC && !$timelineEndDateBC)) {
            // @TODO Do not allow null or empty
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

            $dbDateStart = \DateTime::createFromFormat('Y-m-d', is_string($queryArray[0]['range_start']) ? $queryArray[0]['range_start'] : '0000-01-01');
            $dbValueStart = $dbDateStart->getTimestamp();

            if (is_null($queryArray['range_end'])) {
                $dbDateEndF = (new \DateTime('now'))->format('Y-m-d');
            } else {
                $dbDateEndF = $queryArray['range_end'];
            }

            $dbDateEnd = \DateTime::createFromFormat('Y-m-d', $dbDateEndF);
            $dbValueEnd = $dbDateEnd->getTimestamp();

            // Save (return) only value that points to anyone of range limit
            // Show message only once (e. g., when evaluating range start value)
            if ($value == $timelineStart->getTimestamp()) {
                $this->flashMessage('timeline.invalid_range', 'timeline.details.invalid_range');

                if ($farDateErrorIndex > 0) {
                    $this->flashMessage('timeline.pay_attention',
                    ($farDateErrorIndex == 1 ? 'timeline.details.BC_precedes_AD'
                    : ($timelineStartTStamp < $timelineEndTStamp + self::DAY_TSTAMP ? 'timeline.details.BC_dates_order' : '')), FlashMessage::WARNING);
                }

                return $dbValueStart;
            } else if ($value == $timelineEnd->getTimestamp()) {
                if ($farDateErrorIndex == 2) {
                    return $dbValueStart - self::DAY_TSTAMP;
                }

                return $dbValueEnd;
            }

            return $value;
        }

        return $value;
    }

    /**
     * Get file with localization
     *
     * @return string
     */
    protected static function getFileLocale(): string
    {
        $lang = $GLOBALS['BE_USER']->uc['lang'] ?? '';
        $lang = $lang == 'default' ? '' : $lang . '.';

        return 'LLL:EXT:ak_timeline/Resources/Private/Language/' . $lang . 'locallang.xlf';
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Flash message in BE
     * @param string $keyTitle
     * @param string $keyText
     * @param int $severity
     */
    protected function flashMessage($keyTitle, $keyText, $severity = FlashMessage::ERROR)
    {
        $locale = $this->getFileLocale();
        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
            strlen($keyText) == 0 ? '' : $this->getLanguageService()->sL($locale . ':flashmessage.' . $keyText),
            $this->getLanguageService()->sL($locale . ':flashmessage.' . $keyTitle),
            $severity, true);

        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($flashMessage);
    }
}

