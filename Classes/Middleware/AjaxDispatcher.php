<?php

/**
 * Timeline Controller
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

// NOTE Used currently for tests

declare(strict_types=1);

namespace AK\TimelineVis\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use \AK\TimelineVis\Domain\Repository\TimelineRepository;
use \AK\TimelineVis\Domain\Repository\PointRepository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\LogManager;

class AjaxDispatcher implements MiddlewareInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /**
     * Timeline repository
     *
     * @var TimelineRepository
     */
    private $timelineRepository;

    /**
     * Must be called by ObjectManager, because of EventRepository which has inject methods
     */
    // public function __construct(ResponseFactoryInterface $responseFactory, TimelineRepository $timelineRepository) {
    //     $this->responseFactory = $responseFactory;
    //     $this->timelineRepository = $timelineRepository;
    // }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (!isset($request->getQueryParams()['tlinfo']) || !isset($request->getQueryParams()['pid'])) {
            return $response;
        }

        $pid = $request->getQueryParams()['pid'];

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $logger->warning('resp '. $pid);

        $queryImage = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_timelinevis_domain_model_timeline');
        $queryArray = $queryImage
            ->select('tx_timelinevis_domain_model_timeline' . '.uid','title','range_start','range_end')
            ->where(
                $queryImage->expr()->in('pid', $pid)
            )
            ->from('tx_timelinevis_domain_model_timeline')
            ->execute()->fetchAll();

        // $this->timelineRepository = GeneralUtility::makeInstance(TimelineRepository::class);
        // $result = $this->timelineRepository->findTimeline('pid', $request->getQueryParams()['offset']);

        return new JsonResponse($queryArray);
    }
}
