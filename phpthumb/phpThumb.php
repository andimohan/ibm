<?php
// Public phpThumb endpoint for Composer setup

require __DIR__ . '/../assets/vendor/autoload.php';

// Manually load phpThumb main script
require __DIR__ . '/../assets/vendor/phpThumb/phpThumb.php';

// Pass all query parameters to phpThumb exactly as received
$phpThumb = new phpThumb();

foreach ($_GET as $key => $value) {
    $phpThumb->setParameter($key, $value);
}

if ($phpThumb->GenerateThumbnail()) {
    $phpThumb->RenderOutput();
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Thumbnail generation failed.';
}
?>
