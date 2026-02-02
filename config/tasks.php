<?php

return [
    // The value 30 is the default parameter that php can roll back to if env did not assign a days value.
    'archive_after_days' => env('TASK_ARCHIVE_AFTER_DAYS', 30),
];
