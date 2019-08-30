<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\RateLimit\Exception\RateLimitException;
use Nette\Schema\ValidationException;
use Nette\Utils\AssertionException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof ValidationException || $throwable instanceof AssertionException) {
            return $response->withStatus(400)
                            ->withBody(new SwooleStream(json_encode(['message' => $throwable->getMessage()])));
        }

        if ($throwable instanceof RateLimitException) {
            return $response->withStatus(503)
                            ->withBody(new SwooleStream(json_encode(['message' => $throwable->getMessage()])));
        }

        if ($throwable instanceof ModelNotFoundException) {
            return $response->withStatus(404)
                            ->withBody(new SwooleStream(json_encode([
                                'message' => 'not found'
                            ])));
        }
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(),
            $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        return $response->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
