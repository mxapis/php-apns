<?php declare(strict_types=1);

namespace MXAPIS\APNS\Safari;

use ErrorException;
use MXAPIS\APNS\Certificate;
use ZipArchive;

class PackageGenerator
{
    /** @var Certificate */
    protected $certificate;

    /** @var string */
    protected $basePushPackagePath;


    public function __construct(
        Certificate $certificate,
        $basePushPackagePath
    ) {
        $this->certificate = $certificate;
        $this->basePushPackagePath = $basePushPackagePath;
    }

    public function createPushPackage($websiteId)
    {
        $packageDir = sprintf('/%s/pushPackage%s.%s', sys_get_temp_dir(), time(), $websiteId);
        $package = $this->createPackage($packageDir);

        $this->generatePackage($package);

        return $package;
    }

    private function generatePackage(Package $package)
    {
        $packageDir = $package->getPackageDir();
        $zipPath = $package->getZipPath();

        if (!is_dir($packageDir)) {
            mkdir($packageDir);
        }

        $this->copyPackageFiles($package);
        $this->createPackageManifest($package);
        $this->createPackageSignature($package);

        $zip = $this->createZipArchive();

        if (!$zip->open($zipPath, ZipArchive::CREATE)) {
            throw new ErrorException(sprintf('Could not open package "%s"', $zipPath));
        }

        $packageFiles = Package::$packageFiles;
        $packageFiles[] = 'manifest.json';
        $packageFiles[] = 'signature';

        foreach ($packageFiles as $packageFile) {
            $filePath = sprintf('%s/%s', $packageDir, $packageFile);

            if (!file_exists($filePath)) {
                throw new ErrorException(sprintf('File does not exist "%s"', $filePath));
            }

            $zip->addFile($filePath, $packageFile);
        }

        if (false === $zip->close()) {
            throw new ErrorException(sprintf('Could not save package "%s"', $zipPath));
        }
    }

    private function copyPackageFiles(Package $package)
    {
        $packageDir = $package->getPackageDir();

        mkdir($packageDir . '/icon.iconset');

        foreach (Package::$packageFiles as $rawFile) {
            $filePath = sprintf('%s/%s', $packageDir, $rawFile);

            copy(sprintf('%s/%s', $this->basePushPackagePath, $rawFile), $filePath);
        }
    }

    private function createPackageManifest(Package $package)
    {
        return $this->createPackageManifester()->createManifest($package);
    }

    private function createPackageSignature(Package $package)
    {
        return $this->createPackageSigner()->createPackageSignature(
            $this->certificate, $package
        );
    }

    protected function createPackageSigner()
    {
        return new PackageSigner();
    }

    protected function createPackageManifester()
    {
        return new PackageManifester();
    }

    protected function createZipArchive()
    {
        return new ZipArchive();
    }

    protected function createPackage($packageDir)
    {
        return new Package($packageDir);
    }
}