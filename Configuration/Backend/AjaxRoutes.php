<?php

/**
 * Definitions for routes provided by EXT:ak_timeline
 */
return [
    'timelinevis_dispatch' => [
        'path' => '/ak_timeline/ajax',
        'target' => \AK\TimelineVis\Controller\AjaxDispatcher::class . '::dispatch'
    ]
];
