<?php

declare(strict_types=1);

/*
 * Class checks entered Point date is within Timeline range
 * 
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Evaluation;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Localization\LanguageService;
use \AK\TimelineVis\Timeline\FarDate\FarDate;

class PointValidator
{
    private const DAY_TSTAMP = 86400;
    private const EXT_KEY = 'ak_timeline';

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

        $timelineStartDateBC = (bool)$timeline['date_start_b_c'];
        $timelineEndDateBC = (bool)$timeline['date_end_b_c'];

        $timelineStartTStamp = 0;
        $timelineEnd = new \DateTime(is_string($timeline['range_end']) ? $timeline['range_end'] : 'now');
        $timelineEndTStamp = $timelineEnd->getTimestamp();
        $farDateError = false;

        $locale = $this->getFileLocale();

        // Case Range start date is not specified
        if (strlen($timeline['range_start']) == 0) {
            // At least one limit should be defined
            if (strlen($timeline['range_end']) == 0) {
                // Then error is in Timeline range. FlashMessage is in Timeline validator
                // default value is UNIX epoch
                return 0;
            }

            // Set Far date as positive number
            $farDateVObj = new FarDate((int)$value, $timelineEndDateBC);
            $farDateV = $farDateVObj->getFarDateTimestamp();

            $farDateEnd = (new FarDate($timelineEnd->getTimestamp(), $timelineEndDateBC))->getFarDateTimestamp();

            if ($farDateV > $farDateEnd + self::DAY_TSTAMP) {
                $farDateError = true;
            }
    
            if ($farDateError) {
                $detailsMsg = [
                    $this->getLanguageService()->sL($locale . ':flashmessage.point.details.input_was'),
                    $farDateVObj->logDate(),
                    '. ' . $this->getLanguageService()->sL($locale . ':flashmessage.point.warn.BC_date')
                ];
    
                $this->flashMessage('point.date_is_out', $detailsMsg);
        
                // Save value because it may be right
                return $timelineEnd->getTimestamp();
            }

            $titleMsg = [
                $this->getLanguageService()->sL($locale . ':flashmessage.point.one_point_date') . '"',
                $farDateVObj->logDate(),
                '"'
            ];
    
            $this->flashMessage($titleMsg, 'point.warn.not_out', FlashMessage::INFO);

            return $value;
        } else {
            $timelineStart = new \DateTime($timeline['range_start']);
            $timelineStartTStamp = $timelineStart->getTimestamp();
        }

        // Case of usual D. C. range
        if (!$timelineStartDateBC) {
            if ($value < $timelineStartTStamp - self::DAY_TSTAMP || $value > $timelineEndTStamp) {
                $detailsMsg = [
                    $this->getLanguageService()->sL($locale . ':flashmessage.point.details.input_was'),
                    (new \DateTime())->setTimestamp((int)$value)->format('Y-m-d')
                ];

                $this->flashMessage('point.date_is_out', $detailsMsg);
    
                return $timelineStartTStamp;
            }
    
            return $value;
        }

        // Considering B. C. dates
        $farDateStart = (new FarDate($timelineStartTStamp, $timelineStartDateBC))->getFarDateTimestamp();
        $farDateEnd = (new FarDate($timelineEndTStamp, $timelineEndDateBC))->getFarDateTimestamp();

        $valueBC = false;
        $valueDate = (new \DateTime())->setTimestamp((int)$value)->format('Y-m-d');

        foreach ($formData['tx_timelinevis_domain_model_point'] as $item) {
            if ((new \DateTime($item['pointdate']))->format('Y-m-d') === $valueDate) {
                $valueBC = (bool)$item['pointdate_b_c'];
            }
        }

        $farDateVObj = new FarDate((int)$value, $valueBC);
        $farDateV = $farDateVObj->getFarDateTimestamp();

        if ($farDateV < $farDateStart || $farDateV > $farDateEnd + self::DAY_TSTAMP) {
            $farDateError = true;
        }

        if ($farDateError) {
            $detailsMsg = [
                $this->getLanguageService()->sL($locale . ':flashmessage.point.details.input_was'),
                $farDateVObj->logDate(),
                '. ' . $this->getLanguageService()->sL($locale . ':flashmessage.point.warn.BC_date')
            ];

            $this->flashMessage('point.date_is_out', $detailsMsg);
    
            // Save value because it may be right
            return $timelineStartTStamp;
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

        return 'LLL:EXT:' . self::EXT_KEY . '/Resources/Private/Language/' . $lang . 'locallang_be.xlf';
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
        $titleMessage = gettype($keyTitle) == 'array'
            ? implode($keyTitle)
            : $this->getLanguageService()->sL($locale . ':flashmessage.' . $keyTitle);
        $detailsMessage = gettype($keyText) == 'array'
            ? implode($keyText)
            : $this->getLanguageService()->sL($locale . ':flashmessage.' . $keyText);

        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
            $detailsMessage,
            $titleMessage,
            $severity, true);

        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($flashMessage);
    }
}

