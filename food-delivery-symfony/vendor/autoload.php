<?php

// Simple autoloader for development
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
