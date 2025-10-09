<?php

return [
    // Force GD backend to avoid Imagick dependency
    'image_backend' => 'gd',

    // Default size when not specified
    'size' => 256,

    // Default margin
    'margin' => 1,
];
