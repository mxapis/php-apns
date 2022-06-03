<?php declare(strict_types=1);

namespace MXAPIS\APNS\Safari;

use ErrorException;
use MXAPIS\APNS\Certificate;
use RuntimeException;

class PackageSigner
{
    public function createPackageSignature(Certificate $certificate, Package $package)
    {
        $pkcs12 = $certificate->getCertificateString();
        $certPassword = $certificate->getPassword();
        $certs = array();
        if (!openssl_pkcs12_read($pkcs12, $certs, $certPassword)) {
            throw new RuntimeException('Failed to create signature.');
        }
        $signaturePath = sprintf('%s/signature', $package->getPackageDir());
        $manifestJsonPath = sprintf('%s/manifest.json', $package->getPackageDir());
        // Sign the manifest.json file with the private key from the certificate
        $certData = openssl_x509_read($certs['cert']);
        $privateKey = openssl_pkey_get_private($certs['pkey'], $certPassword);
        openssl_pkcs7_sign($manifestJsonPath, $signaturePath, $certData, $privateKey, array(), PKCS7_BINARY | PKCS7_DETACHED, __DIR__ . '/../../resources/AppleWWDRCAG4.pem');
        // Convert the signature from PEM to DER
        $signaturePem = file_get_contents($signaturePath);
        $matches = array();
        if (!preg_match('~Content-Disposition:[^\n]+\s*?([A-Za-z0-9+=/\r\n]+)\s*?-----~', $signaturePem, $matches)) {
            throw new ErrorException('Failed to extract content from signature pem.');
        }
        $signatureDer = base64_decode($matches[1]);
        file_put_contents($signaturePath, $signatureDer);
        return $signaturePath;
    }
}