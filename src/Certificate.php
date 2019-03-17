<?php declare(strict_types=1);

namespace MXAPIS\APNS;

class Certificate
{
    /** @var string */
    private $certificateString;

    /** @var string */
    private $password;

    public function __construct(string $certificateString, string $password = null)
    {
        $this->certificateString = $certificateString;
        $this->password = $password;
    }

    public function certificateString(): string
    {
        return $this->certificateString;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function writeTo(string $path): void
    {
        file_put_contents($path, $this->certificateString);
    }

    public function writeToTmp(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'cert_');
        $this->writeTo($path);
        return $path;
    }

    public function __toString(): string
    {
        return $this->certificateString;
    }
}