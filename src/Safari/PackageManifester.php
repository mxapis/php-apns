<?php declare(strict_types=1);

namespace MXAPIS\APNS\Safari;

class PackageManifester
{
    const HASH_TYPE = 'sha512';

    public function createManifest(Package $package): string
    {
        $manifestData = [];
        foreach (Package::$packageFiles as $rawFile) {
            $filePath = sprintf('%s/%s', $package->getPackageDir(), $rawFile);
            $manifestData[$rawFile] = [
                'hashType' => self::HASH_TYPE,
                'hashValue' => hash(self::HASH_TYPE, file_get_contents($filePath)),
            ];
        }
        $manifestJsonPath = sprintf('%s/manifest.json', $package->getPackageDir());
        $manifestJson = json_encode((object)$manifestData);
        file_put_contents($manifestJsonPath, $manifestJson);
        return $manifestJsonPath;
    }
}