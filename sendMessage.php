<?php

$obj = array('status' => -1);

// validate input data
if (!isset($_POST['msg']) || !strlen($_POST['msg']) || !isset($_POST['user']) || !strlen($_POST['user'])) {
	$obj['message'] = 'Missing parameter';
	
	http_response_code(422);
	header('Content-Type: application/json');
	echo json_encode($obj);
	exit;
}

// setup shared memory
$shm_key = ftok(__DIR__, 't');
$shm_id = shmop_open($shm_key, "c", 0644, 10 * 1024);
error_log(print_r($shm_id,true));
if (!$shm_id) {
	// failed open shared memory
	$obj['message'] = 'Write Fialed';
	
	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode($obj);
	exit;
}

// build message object
$data = array('user' => filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS),
	'message' => filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_SPECIAL_CHARS),
	'timestamp' => date('Y-m-d H:i:s'));

$ori_str = rtrim(shmop_read($shm_id, 0, shmop_size($shm_id)), "\0");

// read previous messages from shared memory
if (strlen($ori_str)) {
	$ori_data = json_decode($ori_str, true) ?: array();
} else $ori_data = array();

// push new message
array_push($ori_data, $data);

// if too many messages, delete previous messages
$limit = 50;
$cnt = count($ori_data);

error_log($cnt);
error_log($limit);
if ($cnt > $limit) {
	$ori_data = array_slice($ori_data, $cnt - $limit);
}

// write back to the shared memory
$byte = shmop_write($shm_id, json_encode($ori_data), 0);
if (!$byte) {
	// failed writing shared memory
	$obj['message'] = 'Write Fialed';
	
	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode($obj);
	exit;
}

$obj['status'] = 0;
	
header('Content-Type: application/json');
echo json_encode($obj);
?>