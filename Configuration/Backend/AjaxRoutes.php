<?php

/**
 * Definitions for routes provided by EXT:ak_timeline
 */
return [
    'timelinevis_dispatch' => [
        'path' => '/ak_timeline/dispatch',
        'target' => \AK\TimelineVis\Controller\AjaxController::class . '::dispatchAction'
    ]
];

// return [
//     'frontend' => [
//         'AK/my_sitepackage/ajaxdispatcher' => [
//             'target' => \Vendor\MySitepackage\Middleware\AjaxDispatcher::class,
//             'after' => [
//                 'typo3/cms-frontend/prepare-tsfe-rendering'
//             ]
//         ],
//     ],
// ];
