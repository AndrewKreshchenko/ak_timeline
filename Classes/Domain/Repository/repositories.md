
## Please consider steps, if your current Typo3 version is 10 or lower.

1. Change occurences in all *Repository files in the current directory:
```
$querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class); // NOTE for Typo3 4.3 (2009) $this->objectManager->get(Typo3QuerySettings::class);
```
2. If after you set up the plugin you still face up an error of makeInstanse, try to do the similar replacement in the whole plugin code.

------------------


Do not hesitate to give a feedback or ask.
Andrii, mail2andyk@gmail.com
