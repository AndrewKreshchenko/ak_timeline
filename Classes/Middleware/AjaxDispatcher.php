<?php

// NOTE Used currently for tests

declare(strict_types=1);

namespace AK\TimelineVis\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use \AK\TimelineVis\Domain\Repository\TimelineRepository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (!isset($request->getQueryParams()['tlinfo']) || !isset($request->getQueryParams()['pid'])) {
            return $response;
        }

        $pid = $request->getQueryParams()['pid'];
        $table = 'tx_timelinevis_domain_model_timeline';

        $queryImage = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryArray = $queryImage
            ->select($table . '.uid','title','range_start','range_end')
            ->where(
                $queryImage->expr()->in('pid', $pid)
            )
            ->from($table)
            ->execute()->fetchAll();

        return new JsonResponse($queryArray);
    }
}
