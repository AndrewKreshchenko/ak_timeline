<?php

namespace AK\TimelineVis\ViewHelpers;

/**
 * This file is part of the package ak/ak-timelinevis.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * 
 * INFO The viewhelper has a part of functionality got from core DateTimeViewHelper:
 * https://github.com/TYPO3/typo3/blob/main/typo3/sysext/fluid/Classes/ViewHelpers/Format/DateViewHelper.php
 * Extended logic by accepting more options for far (ancient) dates
 * In addition, gives accommodation for PHP of version 8 and bigger
 */
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
// use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ViewHelper to get the extended date
 * 
 * Formats an object implementing :php:`\DateTimeInterface`.
 *
 * Examples
 * ========
 *
 * Defaults
 * --------
 *
 * ::
 *
 * <tln:fardate>{dateObject}</tln:fardate>
 * (the same as when using standard DateViewHelper <f:format.date>{dateObject}</f:format.date>)
 *
 * ``1980-12-13``
 * Depending on the current date.
 *
 *
 * Localized dates using strftime date format
 * ------------------------------------------
 *
 * ::
 *
 * <tln:fardate format="%d %B %Y" isbc="{obj.flagBC}">{dateObject}</tln:fardate>
 *
 * Depending on the "isbc" argument, the date will display with or without a day part.
 * For instance, when date is 02-03-0134 and "isbc" is true, the date will display as "March 134 B. C.".
 * When "isbc" is false, it will be "02 March 134"
 */

// @TODO output with LocalizationUtility

class FardateViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('date', 'mixed', 'Either an object implementing DateTimeInterface or a string that is accepted by DateTime constructor');
        $this->registerArgument('format', 'string', 'Format String which is taken to format the Date/Time', false, '');
        $this->registerArgument('base', 'mixed', 'A base time (an object implementing DateTimeInterface or a string) used if $date is a relative date specification. Defaults to current time.');
        $this->registerArgument('isbc', 'boolean', 'Indicator date is before Christ', false, false);
        $this->registerArgument('isaround', 'boolean', 'Indicator time is approximate', false, false);
        $this->registerArgument('iscenture', 'boolean', 'Indicator time is a centure', false, false);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $format = $arguments['format'] ?? '';
        $base = $arguments['base'] ?? GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp');
        $is_bc = $arguments['isbc'];
        $iscenture = $arguments['iscenture'];
        // NOTE localize
        $prefix = $arguments['isaround'] ? 'around ' : '';

        if (is_string($base)) {
            $base = trim($base);
        }

        if ($format === '') {
            $format = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] ?: 'Y-m-d';
        }

        $date = $renderChildrenClosure();
        if ($date === null) {
            return '';
        }

        if (is_string($date)) {
            $date = trim($date);
        }

        if ($date === '') {
            $date = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp', 'now');
        }

        if (!$date instanceof \DateTimeInterface) {
            try {
                $base = $base instanceof \DateTimeInterface ? (int)$base->format('U') : (int)strtotime((MathUtility::canBeInterpretedAsInteger($base) ? '@' : '') . $base);
                $dateTimestamp = strtotime((MathUtility::canBeInterpretedAsInteger($date) ? '@' : '') . $date, $base);
                $date = new \DateTime('@' . $dateTimestamp);
                $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            } catch (\Exception $exception) {
                throw new Exception('"' . $date . '" could not be parsed by \DateTime constructor: ' . $exception->getMessage(), 1241722579);
            }
        }

        if (str_contains($format, '%')) {
            // strftime is deprecated starting from PHP 8.1: https://stackoverflow.com/questions/70930824/php-8-1-strftime-is-deprecated
            $php_upper_ver = version_compare(PHP_VERSION, '8.1.0') < 0;

            if ($is_bc) {
                return $php_upper_ver ? $prefix . @strftime("%B %Y", (int)$date->format('U')) . ' B. C.' : $prefix . date("F Y", strtotime($date->format('U'))) . ' B. C.';
            } else {
                return $php_upper_ver ? $prefix . @strftime($format, (int)$date->format('U')) : $prefix . date("d F Y", strtotime($date->format('U')));
            }
        }

        if ($is_bc) {
            return $prefix . $date->format("F Y") . ' B. C.';
        }

        if ($iscenture) {
            return $prefix . ceil((int)$date->format("Y") / 100) . ' century';
        }

        return $prefix . $date->format($format);
    }
}
