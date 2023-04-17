
## Please consider steps, if your current Typo3 version is 10 or lower.

1. For Typo3 v. 10 or lower replace occurences in all *Repository files in the current directory:
from `GeneralUtility::makeInstance(Typo3QuerySettings::class)` to `$this->objectManager->get(Typo3QuerySettings::class)`.

2. If after you set up the plugin you still face up an error of makeInstanse, try to do the similar replacement in the whole plugin code.

------------------


Do not hesitate to ask or to give a feedback.
Andrii, mail2andyk@gmail.com
