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
         $this->pointRepository = $pointRepository;
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
        $this->pointRepository->setDefaultQuerySettings($querySettings);
    }

    /**
     * List all points
     */
    public function listAction(): void
    {
        $points = $this->pointRepository->findAll();
        $query = $points->getQuery();
        $query->setOrderings(['pointdate' => QueryInterface::ORDER_DESCENDING]);
        $this->view->assign('points', $query->execute());
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
        $this->pointRepository->remove($point);
        $this->redirect('points');
    }
}
