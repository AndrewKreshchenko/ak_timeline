<?php

/**
 * Point Controller
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis\Controller;

// use ExtbaseBook\TimelineVis\Controller\AbstractBackendController;
use AK\TimelineVis\Domain\Repository\PointRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Point controller class
 */
class PointController extends ActionController
{
    /**
     * Point repository
     *
     * @var PointRepository
     */
    protected $pointRepository;

    /**
     * Import comment repository by dependency injection
     *
     * @param PointRepository $pointRepository
     */
    public function injectPointRepository(PointRepository $pointRepository): void
    {
         $this->PointRepository = $pointRepository;
    }

    /**
     * Initialize view
     *
     * @param ViewInterface
     */
    protected function initializeView(ViewInterface $view): void
    {
        if ($view instanceof BackendTemplateView) {
            /** @var BackendTemplateView $view */
            parent::initializeView($view);
            $this->generateMenu();
        }
    }

    /**
     * Initialize action
     */
    public function initializeAction(): void
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $querySettings->setIgnoreEnableFields(true);
        $this->PointRepository->setDefaultQuerySettings($querySettings);
    }

    /**
     * List all points
     */
    public function listAction(): void
    {
        $points = $this->PointRepository->findAll();
        $query = $points->getQuery();
        $query->setOrderings(['pointdate' => QueryInterface::ORDER_DESCENDING]);
        $this->view->assign('points', array('key' => 123));
        $this->view->assign('limit', $limit);

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $logger->warning('Points key ');
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
    private function paginate(int $timelineUid, int $currentPage = 1) {
        $limit = ($this->settings['pagesCount']) ?: null;

        // $items = GeneralUtility::makeInstance(ObjectStorage::class) (Timeline $timeline)
        // $items = $this->itemService->getItemsByIds($itemIds);
        $items = $this->PointRepository->findLastRecordCreated($timelineUid);

        // $arrayPaginator = new ArrayPaginator($items, 1, $limit);
        // $paging = new SimplePagination($arrayPaginator);
        // $this->view->assignMultiple(
        //     [
        //         'items' => $items,
        //         'paginator' => $arrayPaginator,
        //         'paging' => $paging,
        //         'pages' => range(1, $paging->getLastPageNumber()),
        //     ]
        // );

        // $itemsPerPage = 1;
        // $paginator = new \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator($items, $currentPage, $itemsPerPage);
        // $pagination = new \GeorgRinger\NumberedPagination\NumberedPagination($paginator, 10);
        // $this->view->assign('pagination', [
        //     'paginator' => $paginator,
        //     'pagination' => $pagination,
        // ]);
        $this->view->assign('itemss', $items);

        // $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        // $logger->warning('items page ' . gettype($timelineUid) . $timelineUid);
    }

    /**
     * Show confirmation form before deleting a Point
     *
     * @param Point $point
     */
    public function deleteConfirmAction(Point $point)
    {
        $this->view->assign('points', $point);
    }

    /**
     * Delete a Point from the Point repository
     *
     * @param Point $point
     */
    public function deleteAction(Point $point): void
    {
        $this->PointRepository->remove($point);
        $this->redirect('points');
    }
}
