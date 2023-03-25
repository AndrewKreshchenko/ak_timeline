<?php

// NOTE Used currently for tests

return [
  'frontend' => [
        'ak/timelinevis/ajaxdispatcher' => [
            // Open up API requests
            'target' => \AK\TimelineVis\Middleware\AjaxDispatcher::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering'
            ]
        ],
    ],
];
