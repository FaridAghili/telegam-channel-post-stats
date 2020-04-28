<?php

require_once 'vendor/autoload.php';
require_once 'ChannelPost.php';

use Carbon\Carbon;

$channel = 'pezeshkanghanon';
$startId = 24943;
$endDate = Carbon::now()->subMonths(6);

do {
    $post = new ChannelPost($channel, $startId--);
    if (! $post->init()) {
        continue;
    }

    $row = $post->getViews().','.$post->getId()."\n";
    file_put_contents('stats.csv', $row, FILE_APPEND);

    if ($post->getDateTime()->isBefore($endDate)) {
        break;
    }
} while (true);
