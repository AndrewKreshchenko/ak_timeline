<?php

/**
 * Timeline Controller
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 * 
 * @TODO make version for Typo3 10, considering migration v. 11.0 and following the rules PSR-17
 */

namespace AK\TimelineVis\Controller;

use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \AK\TimelineVis\Domain\Model\Timeline;
use \AK\TimelineVis\Domain\Repository\TimelineRepository;
use \AK\TimelineVis\Domain\Repository\PointRepository;
use \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use \GeorgRinger\NumberedPagination\NumberedPagination;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Timeline controller class
 */
class TimelineController extends ActionController
{
    /**
     * Timeline repository
     *
     * @var TimelineRepository
     */
    protected $timelineRepository;

    /**
     * Timeline repository
     *
     * @var PointRepository
     */
    protected $pointRepository;

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
     * Import Point repository by dependency injection
     *
     * @param PointRepository $pointRepository
     */
    public function injectPointRepository(PointRepository $pointRepository): void
    {
        $this->PointRepository = $pointRepository;
    }

    /**
     * List Timelines
     * Used in TestDemand plugin
     *
     * @param string $search
     */
    public function listAction($search = ''): void
    {
        $search = '';

        if ($this->request->hasArgument('search')) {
            $search = $this->request->getArgument('search');
        }

        $this->view->assign('timelines', $this->TimelineRepository->findSearchedTimeline($search));
        $this->view->assign('lastTimeline', $this->TimelineRepository->findLastRecordCreated());
    }

    /**
     * Show a single Timeline
     * 
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation int $currentPage
     */
    public function showAction(int $currentPage = 1): void
    {
        $pageArguments = $this->request->getAttribute('routing');
        $result = $this->TimelineRepository->findTimeline('pid', $pageArguments['pageId']);

        // Other timelines that have relation to current timeline
        $segments = null;

        if ($result && !$this->settings['disableDerivedPoints']) {
            $segments = $this->TimelineRepository->findTimelinesSegments($result->getUid());
        }

        if ($this->settings['enablePagination'] && !is_null($result)) {
            $this->paginate((int)$result->getUid(), $currentPage > 1 ? $currentPage : 1);
        }

        // Make odered points list depending on style
        if ($result && !is_int(strpos($this->settings['timeline']['style'], 'horiz'))) {
            $resultPoints = $this->orderPoints($result, (int)$result->getUid());

            $this->view->assign('timelinePoints', $resultPoints);
        }

        // Set respective widget visible if exists
        if (strlen(($this->settings['widget']['type'])) > 0) {
            $widgets = explode(',', $this->settings['widget']['type']);
            $this->view->assign('widgets', $widgets);
        }

        $this->view->assign('timeline', $result);
        $this->view->assign('segments', $segments);
    }

    /**
     * Dispatch request
     * 
     */
    public function dispatchAction()
    {
        $result = array();
        $timelineId = (int)$this->request->getArgument('id');

        if (is_int($timelineId)) {
            $data = $this->PointRepository->getPointData($timelineId);
            $result['points'] = $data;
        }

        // @TODO implement for old Typo3 versions
        $response = $this->responseFactory->createResponse()->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($result, JSON_THROW_ON_ERROR));

        return $response;
    }

    /**
     * Helper method. get points
     * 
     * @param Timeline|null $timeline
     * @param int $timelineId - timeline ID
     * @return array|null
    */
    protected function orderPoints(Timeline $timeline, int $timelineId)
    {
        if (!is_int($timelineId)) {
            return null;
        }

        $points = $timeline->getSortedPoints($timelineId);

        return $points;
    }

    /**
     * Pagination provider
     * 
     * Used examples / tutorials:
     * https://t3planet.com/blog/typo3-tutorials/migrate-typo3-fluid-to-native-paginatorinterface
     * https://www.in2code.de/aktuelles/pagebrowser-viewhelper-in-typo3-11/
     * 
     * @param int $timelineUid
     * @param int $currentPage
    */
    protected function paginate(int $timelineUid, int $currentPage) {
        $items = $this->PointRepository->findPointsByTimelineUid($timelineUid);
        $paginator = new QueryResultPaginator($items, $currentPage, ($this->settings['itemsPerPage']) ?: 49);
        $pagination = new NumberedPagination($paginator, ($this->settings['pagesCount']) ?: 99);

        $this->view->assign('pagination', [
            'paginator' => $paginator,
            'pagination' => $pagination,
        ]);
    }
}
