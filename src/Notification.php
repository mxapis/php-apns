<?php declare(strict_types=1);

namespace MXAPIS\APNS;

use ZendService\Apple\Apns\Message\Alert;
use ZendService\Apple\Apns\Message;

class Notification implements ClientNotification
{
    protected $apnsMessage;

    public function __construct(
        string $deviceToken,
        string $title,
        string $body,
        array $urlArgs,
        string $identifier = null
    )
    {
        $alert = new Alert();
        $alert->setTitle($title);
        $alert->setBody($body);
        $message = new Message();
        $message->setId($identifier ?: sha1($deviceToken . $title));
        $message->setAlert($alert);
        $message->setToken($deviceToken);
        $message->setUrlArgs($urlArgs);

        $this->apnsMessage = $message;
    }

    public function deviceToken(): string
    {
        return $this->apnsMessage->getToken();
    }

    public function identifier(): string
    {
        return $this->apnsMessage->getId();
    }

    public function getPayloadJson(): string
    {
        return json_encode($this->apnsMessage->getPayload());
    }
}