<?php
// set a function to output stream data
function stream($event, $obj)
{
    echo "event: $event\ndata:".json_encode($obj)."\n\n";
	@flush();
	@ob_flush();
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    return;
}

function endSSE()
{
	echo "event:message\ndata: END-OF-STREAM\n\n"; // Give browser a signal to stop re-opening connection
	ob_get_flush();
	flush();
	sleep(1);
}

// set timeout
@ini_set('default_socket_timeout',-1);
set_time_limit(0);

// setup buffering of frontend
date_default_timezone_set('Asia/Taipei');
header('X-Accel-Buffering: no');
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

// setup shared memory (use folder as key to shared with other scripts)
$shm_key = ftok(__DIR__, 't');
$shm_id = shmop_open($shm_key, "c", 0644, 10 * 1024);
error_log(print_r($shm_id,true));

if (!$shm_id) {
	// failed open shared memory
	http_response_code(500);
	echo "data: Somthing went wrong!";
	exit;
}

// read messages from shared memory
ob_start();
$data = rtrim(shmop_read($shm_id, 0, shmop_size($shm_id)), "\0");
$rec = json_decode($data, true);
if ($rec) {
	foreach($rec as $msg) {
		// send previous messages
		stream('message', $msg);
		$flag = true;
		if (connection_aborted()) endSSE();
	}
}


// Welcome message
$init = array('user' => 'SYSTEM', 'message' => 'Welcome to the system!');
stream('message', $init);
if (connection_aborted()) endSSE();

$c_cnt = 0;
$idle = 0;
$dt = time();
$first = true;

while(1) {
	// read messages from shared memory
	$data = rtrim(shmop_read($shm_id, 0, shmop_size($shm_id)), "\0");
	$rec = json_decode($data, true);
	if ($rec) {
		$flag = false;
		$tdt = $dt;
		foreach($rec as $msg) {
			// send new messages and update last message time
			$mdt = strtotime($msg['timestamp']);
			if (strtotime($msg['timestamp']) > $dt) {
				stream('message', $msg);
				$flag = true;
				$tdt = $mdt > $tdt ? $mdt : $tdt;
			}
		}
		$dt = $tdt;
		$first = false;
		if (connection_aborted()) endSSE();
		if (!$flag) $idle++;
	} else $idle++;
	
	// prevent timeout, send idle event every 20 sec
	if ($idle >= 200) {
		stream('idle', array('time' => date('Y-m-d H:i:s')));
		$idle = 0;
	}
	
	// loop every 0.1 sec
    usleep(1000 * 1000 / 10);
}

?>