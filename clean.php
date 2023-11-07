<?php
// manually clean up shared memory
$shm_key = ftok(__DIR__, 't');
$shm_id = shmop_open($shm_key, "w", 0, 0);
if (!$shm_id) echo "No such memory";
else if (shmop_delete($shm_id)) echo "Success!";
else echo "Failed!";
?>