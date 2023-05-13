<?php

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Hooks\Backend\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Core\Localization\LanguageService;

class WidgetItemsProcFunc {
  /**
   * Set Widget type
   *
   * @param array &$config configuration array
   */
  public function getWidgetType(array &$config, TcaSelectItems $fObj)
  {
    $locale = $this->getFileLocale();
    // @TODO Provide opportunity for user to handle Timeline styles (backend module)

    $languageService = $this->getLanguageService();

    $config['items'] = [
      [$languageService->sL($locale . ':timeline.select.none'), ''],
      [$languageService->sL($locale . ':widget.type.collapsiblepoints'), 'collapsiblePoints'],
      [$languageService->sL($locale . ':widget.type.formfilter'), 'formFilter'],
      [$languageService->sL($locale . ':widget.type.slider'), 'slider'],
    ];
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

    return 'LLL:EXT:ak_timeline/Resources/Private/Language/' . $lang . 'locallang_be.xlf';
  }

  /**
   * @return LanguageService
   */
  protected function getLanguageService(): LanguageService
  {
    return $GLOBALS['LANG'];
  }
}
