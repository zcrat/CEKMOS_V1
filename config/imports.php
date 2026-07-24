<?php

return [
    'progress_cache_store' => env('IMPORT_PROGRESS_CACHE_STORE', 'file'),
    'progress_ttl_hours' => (int) env('IMPORT_PROGRESS_TTL_HOURS', 48),
];
