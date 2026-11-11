<?php

declare(strict_types=1);

return [
    /*
     * Soft preferences of the draft generator (D-262): on/off per school, no weights.
     * These are the defaults a school starts with.
     */
    'preferences' => [
        'max_consecutive' => true,   // at most 2 JP of a subject per day, 3 for PJOK
        'pjok_before_10' => true,    // PJOK ends by 10.00
        'core_early' => true,        // Matematika and Bahasa Indonesia in the first lessons
        'spread_week' => true,       // a subject's JP on different days
        'no_split_break' => true,    // a subject is not split by a break
    ],

    /* Subjects the switches above name, by catalogue code. */
    'subjects' => [
        'pjok' => ['PJOK'],
        'core' => ['MTK', 'BIN'],
    ],

    /* The latest time PJOK may end with `pjok_before_10`. */
    'pjok_ends_by' => '10:00',

    /* Attempts per run; the best draft is kept (about 10 s per school, D-262). */
    'attempts' => 30,

    /*
     * The day frame a school starts with: a 6-day week (D-323), 35-minute JP, two breaks.
     * The operator changes it on the Susun draf jadwal page.
     */
    'day_frame' => [
        'days' => [
            1 => ['starts_at' => '07:00', 'periods' => 8],
            2 => ['starts_at' => '07:00', 'periods' => 8],
            3 => ['starts_at' => '07:00', 'periods' => 8],
            4 => ['starts_at' => '07:00', 'periods' => 8],
            5 => ['starts_at' => '07:00', 'periods' => 5],
            6 => ['starts_at' => '07:00', 'periods' => 6],
        ],
        'breaks' => [
            ['after_period' => 3, 'minutes' => 15, 'label' => null],
            ['after_period' => 6, 'minutes' => 15, 'label' => null],
        ],
    ],
];
