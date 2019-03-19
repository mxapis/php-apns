<?php declare(strict_types=1);

namespace MXAPIS\APNS;

use GuzzleHttp\Psr7\Response AS HttpResponse;

class ResponseProcessor
{
    /** @var Response[] */
    protected $responses = [];

    public function __construct()
    {
        $this->responses = [];
    }

    public function process(HttpResponse $httpResponse, $identifier) {
        $response = new Response($httpResponse->getStatusCode(), $httpResponse->getHeader('apns-id')[0]);
        $response->setIdentifier($identifier);

        if (!$response->success()) {
            try {
                $body = json_decode($httpResponse->getBody()->getContents());
                $response->setReason($body->reason);
                if ($response->statusCode() === Response::STATUS_UNREGISTERED) {
                    $response->setTimestamp(
                        (new \DateTimeImmutable())->setTimestamp($body->timestamp)
                    );
                }
            } catch (\Exception $e) {}
        }

        $this->responses[] = $response;
    }

    public function responses(): array
    {
        return $this->responses;
    }
}