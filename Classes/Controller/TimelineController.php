<?php

/**
 * Timeline Controller
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis\Controller;

use \TYPO3\CMS\Core\Messaging\AbstractMessage;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use \AK\TimelineVis\Domain\Model\Timeline;
use \AK\TimelineVis\Domain\Repository\TimelineRepository;
use \AK\TimelineVis\Domain\Repository\PointRepository;

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
     * Import Timeline repository by dependency injection
     *
     * @param TimelineRepository $timelineRepository
     */
    public function injectTimelineRepository(TimelineRepository $timelineRepository): void
    {
        $this->TimelineRepository = $timelineRepository;
    }

    /**
     * Index action for this controller. Displays a list of blogs.
     */
    // public function indexAction(int $currentPage = 1): ResponseInterface
    // {
    //     $allAvailableTimelines = $this->timelineRepository->findAll();
    //     $paginator = new QueryResultPaginator(
    //         $allAvailableTimelines,
    //         $currentPage,
    //         3
    //     );
    //     $pagination = new SimplePagination($paginator);
    //     $this->view
    //         ->assign('timelines', $allAvailableTimelines)
    //         ->assign('paginator', $paginator)
    //         ->assign('pagination', $pagination)
    //         ->assign('pages', range(1, $pagination->getLastPageNumber()));
    //     return $this->htmlResponse();
    // }

    /**
     * List Timelines
     *
     * @param string $search
     */
    public function listAction($search = ''): void
    {
        $search = '';
        if ($this->request->hasArgument('search')) {
            $search = $this->request->getArgument('search');
        }
        $limit = ($this->settings['timeline']['max']) ?: null;
        $this->view->assign('timelines', $this->TimelineRepository->findSearchForm($search, $limit));
        $this->view->assign('search', $search);
    }

    /**
     * Show a single Timeline (detail view)
     *
     * @param Timeline $timeline
     */
    public function showAction(): void
    {
        $pageArguments = $this->request->getAttribute('routing');
        $result = $this->TimelineRepository->findTimeline('pid', $pageArguments['pageId']);

        // Other timelines that have relation to current timeline
        $segments = null;

        if ($result) {
            $segments = $this->TimelineRepository->findTimelinesSegments($result->getUid());
        }

        $this->view->assign('timeline', $result);
        $this->view->assign('segments', $segments);
    }

    // NOTE comment everything below
    // Check and clear unneded methods

    /**
     * Show form to add a new Timeline
     *
     * @param Timeline $timeline
     */
    public function addFormAction(Timeline $timeline = null): void
    {
        $this->view->assign('timeline', $timeline);
        $this->view->assign('points', $this->objectManager->get(PointRepository::class)->findAll());
    }

    /**
     * Set TypeConverter option for image upload
     *
     * To prevent exception #1297759968 ("It is not allowed to map property 'tags'"),
     * mapping of property "tags" is explicitly enabled by this method.
     *
     * Note: This is not documented in the TYPO3 Extbase Book, because we assume, reads
     * have created tags via the backend already, so the exception is not triggered.
     */
    public function initializeAddAction(): void
    {
        /** @var PropertyMappingConfiguration $propertyMappingConfiguration */
        $this->setTypeConverterConfigurationForImageUpload('timeline');
        $propertyMappingConfiguration = $this->arguments['timeline']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->allowProperties('points');
    }

    /**
     * Add a new Timeline to the Timeline repository
     *
     * @param Timeline $timeline
     */
    public function addAction(Timeline $timeline): void
    {
        /*
        // Option 1
        $languageFile = 'LLL:EXT:simpleTimeline/Resources/Private/Language/locallang.xlf';
        $flashMessageHeadline = LocalizationUtility::translate(
            $languageFile . ':flashmessage.timeline.timeline-created.headline'
        );
        $flashMessageBody = LocalizationUtility::translate(
            $languageFile . ':flashmessage.timeline.timeline-created.body'
        );
        */

        // Option 2
        $flashMessageHeadline = LocalizationUtility::translate(
            'flashmessage.timeline.timeline-created.headline',
            'ak_timeline'
        );
        $flashMessageBody = LocalizationUtility::translate(
            'flashmessage.timeline.timeline-created.body',
            'ak_timeline'
        );

        $this->TimelineRepository->add($timeline);
        $this->addFlashMessage(
            $flashMessageBody,
            $flashMessageHeadline,
            AbstractMessage::OK,
            true
        );
        $this->redirect('list');
    }

    // @TYPO3\CMS\Extbase\Annotation\Validate(param="timeline", validator="AK\TimelineVis\Domain\Validator\TimelineValidator")

    /**
     * Show form to update an existing Timeline
     *
     * @param Timeline $timeline
     * 
     */
    public function updateFormAction(Timeline $timeline): void
    {
        $this->view->assign('timeline', $timeline);
        $this->view->assign('points', $this->objectManager->get(PointRepository::class)->findAll());
    }

    /**
     * Set TypeConverter option for image upload
     */
    // public function initializeUpdateAction(): void
    // {
    //     $this->setTypeConverterConfigurationForImageUpload('timeline');
    //     $propertyMappingConfiguration = $this->arguments['timeline']->getPropertyMappingConfiguration();
    //     $propertyMappingConfiguration->allowProperties('points');
    // }

    /**
     * Update an existing Timeline in the Timeline repository
     *
     * @param Timeline $timeline
     */
    public function updateAction(Timeline $timeline): void
    {
        $this->TimelineRepository->update($timeline);
        $this->redirect('list');
    }

    /**
     * Show confirmation form before deleting a Timeline
     *
     * @param Timeline $timeline
     */
    public function deleteConfirmAction(Timeline $timeline)
    {
        $this->view->assign('timeline', $timeline);
    }

    /**
     * Delete a Timeline from the Timeline repository
     *
     * @param Timeline $timeline
     */
    public function deleteAction(Timeline $timeline): void
    {
        $this->TimelineRepository->remove($timeline);
        $this->redirect('list');
    }

    // /**
    //  * Set TypeConverter configuration for image upload
    //  *
    //  * @param string
    //  */
    // protected function setTypeConverterConfigurationForImageUpload($argumentName): void
    // {
    //     $uploadConfiguration = [
    //         UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS =>
    //             $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
    //         UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER =>
    //             '1:/simpleTimeline/',
    //     ];
    //     /** @var PropertyMappingConfiguration $propertyMappingConfiguration */
    //     $propertyMappingConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
    //     $propertyMappingConfiguration->forProperty('image')
    //         ->setTypeConverterOptions(
    //             UploadedFileReferenceConverter::class,
    //             $uploadConfiguration
    //         );
    // }

    // /**
    //  * Helper method. Get timeline by specified relation
    // */
    // private function retrieveById(Timeline $timeline)
    // {
    //     return $timeline->getParentId();
    // }

    /**
     * Helper method. Parse range values of Timeline
    */
    private function parseRange(Timeline $timeline)
    {
        return [
            $timeline->getRangeStart()->getTimestamp(),
            $timeline->getRangeEnd()->getTimestamp()
        ];
    }

    /**
     * Order timeline segments
     *
     * @param array $timelines - child timelines as segments
     */
    protected function orderSegments($timelines)
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        // foreach (($timelines ?? []) as $timeline) {
        //     $range = $this->parseRange($timeline);
        //     // $timelineStart = $timeline.getRangeStart(); // new \DateTime(
        //     // $timelineEnd = $timeline.getRangeEnd();

        //     $logger->warning('ranges: ' . implode(' - ', $range));

        //     // if ($timelineStart > $timelineEnd) {
                
        //     // }
        // }

        // Or with usort method
        // $result = usort($timelines, function ($prev, $next) {
        //     $rangePrev = $this->parseRange($prev);
        //     $rangeNext = $this->parseRange($next);
        //     return $rangePrev <=> $rangeNext;
        // });

        // $logger->warning('ranges: ' . implode(' - ', $result));

        return $timelines;
    }
}
