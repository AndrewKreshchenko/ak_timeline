<?php

declare(strict_types=1);

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * Used sources:
 * https://docs.typo3.org/m/typo3/reference-tca/main/en-us/ColumnsConfig/Type/Input/Properties/Eval.html#custom-eval-rules
 * Used example made by maechler (https://stackoverflow.com/questions/42986309/typo3-tca-own-evaluation-validation-for-a-combination-of-three-fields)
 */

namespace AK\TimelineVis\Evaluation;

// use AK\TimelineVis\Converter\TimeToStringConverter;
use \AK\TimelineVis\Domain\Model\Timeline;
use \AK\TimelineVis\Domain\Repository\TimelineRepository;
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
        $timelineEnd = new \DateTime($timeline['range_end']);

        if ($timelineStart > $timelineEnd) {
            $this->flashMessage('Invalid field value ', 'Course end date can not be before timeline start date!');
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

