<?php declare(strict_types=1);

namespace MXAPIS\APNS\Safari;

class Package
{
    /**
     * @var array
     */
    public static $packageFiles
        = [
            'icon.iconset/icon_16x16.png',
            'icon.iconset/icon_16x16@2x.png',
            'icon.iconset/icon_32x32.png',
            'icon.iconset/icon_32x32@2x.png',
            'icon.iconset/icon_128x128.png',
            'icon.iconset/icon_128x128@2x.png',
            'website.json',
        ];
    /**
     * @var string
     */
    private $packageDir;

    /**
     * @var string
     */
    private $zipPath;

    /**
     * @param string $packageDir
     */
    public function __construct($packageDir)
    {
        $this->packageDir = $packageDir;
        $this->zipPath = sprintf('%s.zip', $packageDir);
    }

    /**
     * Gets path to the zip package directory.
     *
     * @return string $packageDir
     */
    public function getPackageDir()
    {
        return $this->packageDir;
    }

    /**
     * Gets path to the zip package.
     *
     * @return string $zipPath
     */
    public function getZipPath()
    {
        return $this->zipPath;
    }
}