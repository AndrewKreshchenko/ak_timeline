<?php
declare(strict_types = 1);

namespace AK\TimelineVis\Controller;

use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \AK\TimelineVis\Domain\Repository\TimelineRepository;
use \AK\TimelineVis\Domain\Model\Timeline;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

class AjaxController extends ActionController
{
    /**
     * Timeline repository
     *
     * @var TimelineRepository
     */
    private $timelineRepository;

    /**
     * Import Timeline repository by dependency injection
     *
     * @param TimelineRepository $timelineRepository
     */
    public function injectTimelineRepository(TimelineRepository $timelineRepository): void
    {
        $this->TimelineRepository = $timelineRepository;
    }

    /**
     * Dispatch request
     */
    public function dispatchAction()
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $logger->warning("Controller AjaxController");

        $result = array();

        if ($this->request->hasArgument('page')) {
            $pageId = $this->request->getArgument('page');
            $result['pageId'] = $pageId;

            // $data = $this->TimelineRepository->findTimeline('pid', $pageId);

            // // Other timelines that have relation to current timeline
            // $segments = null;

            // if ($result) {
            //     $segments = $this->TimelineRepository->findTimelinesSegments($data->getUid());
            // }

            // $result['data'] = $data;
            // $result['segments'] = $segments;
        }

        $response = $this->responseFactory->createResponse()->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($result, JSON_THROW_ON_ERROR));

        return $response;
    }
}
