<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Stripe\\' => array($vendorDir . '/stripe/stripe-php/lib'),
    'RtclPro\\' => array($baseDir . '/app'),
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-factory/src', $vendorDir . '/psr/http-message/src'),
    'Psr\\Http\\Client\\' => array($vendorDir . '/psr/http-client/src'),
    'GuzzleHttp\\Psr7\\' => array($vendorDir . '/guzzlehttp/psr7/src'),
    'GuzzleHttp\\Promise\\' => array($vendorDir . '/guzzlehttp/promises/src'),
    'GuzzleHttp\\' => array($vendorDir . '/guzzlehttp/guzzle/src'),
    'ExpoSDK\\' => array($vendorDir . '/ctwillie/expo-server-sdk-php/src'),
    'AppleSignIn\\' => array($vendorDir . '/griffinledingham/php-apple-signin'),
);
