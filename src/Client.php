<?php declare(strict_types=1);

namespace MXAPIS\APNS;

use GuzzleHttp\Client AS HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response AS HttpResponse;

class Client
{
    /** @var string */
    const HOST_DEVELOPMENT = "https://api.development.push.apple.com";

    /** @var string */
    const HOST_PRODUCTION = "https://api.push.apple.com";

    /** @var Certificate */
    protected $certificate;

    /** @var string */
    protected $host;

    /** @var ClientNotification[] */
    protected $notifications;

    /** @var */
    protected $responses;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public static function newClient(Certificate $certificate): self
    {
        return new static($certificate);
    }

    public function production(): self
    {
        $this->host = self::HOST_PRODUCTION;
        return $this;
    }

    public function add(ClientNotification $notification): void
    {
        $this->notifications[] = $notification;
    }

    public function push(): array
    {
        $certificatePath = $this->certificate->writeToTmp();

        $client = new HttpClient([
            'base_uri'    => $this->host,
            'cert'        => [$certificatePath, $this->certificate->getPassword()],
            'http_errors' => false,
            'headers'     => [
                'Content-Type' => 'application/json; charset=utf-8',
            ],
        ]);

        $responseProcessor = new ResponseProcessor();

        $requests = function ($notifications) {
            foreach ($notifications as $notification) {
                yield  $notification->identifier() => new Request('POST', sprintf('/3/device/%s', $notification->deviceToken()), [],
                    $notification->getPayloadJson(), '2.0');
            }
        };

        $pool = new Pool($client, $requests($this->notifications), [
            'concurrency' => 100,
            'fulfilled'   => function (HttpResponse $response, $identifier) use ($responseProcessor) {
                $responseProcessor->process($response, $identifier);
            },
            'rejected'    => function ($reason, $identifier) use ($responseProcessor) {
                if ($reason instanceof ClientException) {
                    $responseProcessor->process($reason->getResponse(), $identifier);
                }
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();

        $this->responses = $responseProcessor->responses();

        return $this->responses;
    }
}