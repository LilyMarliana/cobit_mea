<?php
// Simple autoloader for TCPDF
spl_autoload_register(function ($class_name) {
    // Check if the class starts with TCPDF
    if (strpos($class_name, 'TCPDF') === 0) {
        $file = __DIR__ . '/vendor/tcpdf/' . strtolower($class_name) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        
        // Also try the main tcpdf.php file for the main class
        if ($class_name === 'TCPDF') {
            require_once __DIR__ . '/vendor/tcpdf/tcpdf.php';
            return;
        }
    }
});
?>