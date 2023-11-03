<?php

function stream($event, $obj) {
    echo "event: $event\ndata:".json_encode($obj)."\n\n";
	@flush();
	@ob_flush();
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    return;
}

@ini_set('implicit_flush',1);

@ob_end_clean();

set_time_limit(0);

date_default_timezone_set('Asia/Taipei');
header('X-Accel-Buffering: no');
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

$limit = 10;

while($limit--) {
    $current_time = date('Y-m-d H:i:s');
    $obj = array('time' => $current_time, 'number' => 10 - $limit);
    stream('message', $obj);
    sleep(2);
    if (connection_aborted()) break;
}

echo "event:message\ndata: END-OF-STREAM\n\n"; // Give browser a signal to stop re-opening connection
ob_get_flush();
flush();
sleep(1);
?>