<?php declare(strict_types=1);

namespace MXAPIS\APNS;

interface ClientNotification
{
    public function deviceToken(): string;

    public function identifier(): string;

    public function getPayloadJson(): string;
}