<?php
/**
 * Autoloader manual para mPDF y dependencias
 * Generado para entorno sin Composer completo
 */

$baseDir = __DIR__;

// PSR-4 namespace map
$namespaceMap = [
    'Mpdf\\PsrLogAwareTrait\\'  => $baseDir . '/mpdf/psr-log-aware-trait/src/',
    'Mpdf\\PsrHttpMessageShim\\' => $baseDir . '/mpdf/psr-http-message-shim/src/',
    'Mpdf\\'                    => $baseDir . '/mpdf/mpdf/src/',
    'Psr\\Http\\Message\\'      => $baseDir . '/psr/http-message/src/',
    'Psr\\Log\\'                => $baseDir . '/psr/log/src/',
    'DeepCopy\\'                => $baseDir . '/myclabs/deep-copy/src/DeepCopy/',
    'setasign\\Fpdi\\'          => $baseDir . '/setasign/fpdi/src/',
];

spl_autoload_register(function ($class) use ($namespaceMap) {
    foreach ($namespaceMap as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) continue;
        $file = $dir . str_replace('\\', '/', substr($class, $len)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Files autoload (funciones globales)
$files = [
    $baseDir . '/mpdf/mpdf/src/functions.php',
    $baseDir . '/myclabs/deep-copy/src/DeepCopy/deep_copy.php',
];
foreach ($files as $file) {
    if (file_exists($file)) require_once $file;
}
