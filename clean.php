<?php
// manually clean up shared memory
$shm_key = ftok(__DIR__, 't');
$shm_id = shmop_open($shm_key, "c", 0644, 10 * 1024);

if (shmop_delete($shm_id)) echo "Success!";
else echo "Failed!";
?>