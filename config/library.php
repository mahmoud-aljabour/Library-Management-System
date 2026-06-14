<?php

return [
    'max_borrowings_per_member' => (int) env('LIBRARY_MAX_BORROWINGS', 3),
    'default_borrow_days' => (int) env('LIBRARY_DEFAULT_BORROW_DAYS', 14),
];
