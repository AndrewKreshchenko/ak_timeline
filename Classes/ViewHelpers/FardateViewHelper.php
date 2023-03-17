<?php

namespace AK\TimelineVis\ViewHelpers;

/**
 * This file is part of the package ak/ak-timelinevis.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * 
 * INFO about the viewhelper
 * A part of functionality got from core DateTimeViewHelper in order to use native format.date options:
 * https://github.com/TYPO3/typo3/blob/main/typo3/sysext/fluid/Classes/ViewHelpers/Format/DateViewHelper.php
 */
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
// use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
// use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

// NOTE remove line
use TYPO3\CMS\Core\Log\LogManager;

use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ViewHelper to get the extended date
 *
 * # Example: Basic Example
 * @TODO provide example
 * @TODO output with LocalizationUtility
 */
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

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

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
            // $logger->warning('strcontains % ' . ($php_upper_ver ? 'I am still lower 8.1' : 'bigger'));

            if ($is_bc) {
                // $logger->warning('there b.c.');
                return $php_upper_ver ? @strftime("%B %Y", (int)$date->format('U')) . ' B. C.' : date("F Y", strtotime($date->format('U'))) . ' B. C.';
            } else {
                // $logger->warning('there ');
                return $php_upper_ver ? @strftime($format, (int)$date->format('U')) : date("d F Y", strtotime($date->format('U')));
            }
        }

        if ($is_bc) {
            return $date->format("F Y") . ' B. C.';
        }

        return $date->format($format);
    }
}
