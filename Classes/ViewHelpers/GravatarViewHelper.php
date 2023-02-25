<?php
namespace AK\TimelineVis\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

// NOTE if class is needed

/**
 * Gravatar ViewHelper class
 */
class GravatarViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Tag name
     */
    protected $tagName = 'img';

    /**
     * Initialize arguments
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('email', 'string', 'Email address to use as Gravatar ID', true);
    }

    /**
     * Render
     *
     * @return string
     */
    public function render(): string
    {
        $email = (string) $this->arguments['email'];
        if (GeneralUtility::validEmail($email)) {
            $gravatarUri = 'https://www.gravatar.com/avatar/' . md5($email);
            $this->tag->addAttribute('src', $gravatarUri);
            return $this->tag->render();
        }
        return '';
    }
}
