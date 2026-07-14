
<?php

$programs = [
  1 => [
    'name'        => 'Beginner Full-Body (3 days)',
    'level'       => 'Beginner',
    'goal'        => 'Muscle gain',
    'duration'    => '8 weeks',
    'days'        => 3,
    'description' => 'Simple three-day full-body routine focused on learning technique and building a base.',
    'schedule'    => [
      'Day 1 – Full Body A' => [
        ['text' => 'Back Squat – 3×8–10',              'exercise_id' => 17],
        ['text' => 'Push-up – 3×AMRAP',                'exercise_id' => 1],
        ['text' => 'Lat Pulldown – 3×10–12',           'exercise_id' => 7],
        ['text' => 'Dumbbell Shoulder Press – 3×10',   'exercise_id' => 13],
        ['text' => 'Plank – 3×30–45 sec',              'exercise_id' => 27],
      ],
      'Day 2 – Full Body B' => [
        ['text' => 'Romanian Deadlift – 3×8–10',       'exercise_id' => 19],
        ['text' => 'Incline Dumbbell Press – 3×8–10',  'exercise_id' => 3],
        ['text' => 'Seated Cable Row – 3×10–12',       'exercise_id' => 9],
        ['text' => 'Dumbbell Lateral Raise – 3×12–15', 'exercise_id' => 14],
        ['text' => 'Crunch – 3×12–15',                 'exercise_id' => 28],
      ],
      'Day 3 – Full Body C' => [
        ['text' => 'Leg Press – 3×10–12',              'exercise_id' => 18],
        ['text' => 'Barbell Bench Press – 3×6–8',      'exercise_id' => 2],
        ['text' => 'One-Arm Dumbbell Row – 3×10–12',   'exercise_id' => 10],
        ['text' => 'Cable Crossover – 3×12–15',        'exercise_id' => 5],
        ['text' => 'Russian Twist – 3×20 reps',        'exercise_id' => 30],
      ],
    ],
  ],

  2 => [
    'name'        => 'Upper / Lower Split (4 days)',
    'level'       => 'Intermediate',
    'goal'        => 'Muscle gain',
    'duration'    => '10 weeks',
    'days'        => 4,
    'description' => 'Two upper-body and two lower-body sessions with moderate volume and progressive overload.',
    'schedule'    => [
      'Day 1 – Upper A' => [
        ['text' => 'Barbell Bench Press – 4×6–8',      'exercise_id' => 2],
        ['text' => 'Lat Pulldown – 4×8–10',            'exercise_id' => 7],
        ['text' => 'Overhead Barbell Press – 3×8–10',  'exercise_id' => 13],
        ['text' => 'Barbell Curl – 3×10–12',           'exercise_id' => 23],
        ['text' => 'Triceps Pushdown – 3×10–12',       'exercise_id' => 25],
      ],
      'Day 2 – Lower A' => [
        ['text' => 'Back Squat – 4×6–8',               'exercise_id' => 17],
        ['text' => 'Romanian Deadlift – 3×8–10',       'exercise_id' => 19],
        ['text' => 'Leg Press – 3×10–12',              'exercise_id' => 18],
        ['text' => 'Leg Curl – 3×12–15',               'exercise_id' => 21],
        ['text' => 'Standing Calf Raise – 3×12–15',    'exercise_id' => 22],
      ],
      'Day 3 – Upper B' => [
        ['text' => 'Incline Dumbbell Press – 4×8–10',  'exercise_id' => 3],
        ['text' => 'Seated Cable Row – 4×8–10',        'exercise_id' => 9],
        ['text' => 'Lateral Raise – 3×12–15',          'exercise_id' => 14],
        ['text' => 'Hammer Curl – 3×10–12',            'exercise_id' => 24],
        ['text' => 'Overhead Triceps Extension – 3×10–12', 'exercise_id' => 26],
      ],
      'Day 4 – Lower B' => [
        ['text' => 'Leg Press – 4×8–10',               'exercise_id' => 18],
        ['text' => 'Romanian Deadlift – 3×8–10',       'exercise_id' => 19],
        ['text' => 'Walking Lunges – 3×10–12 each leg','exercise_id' => 17], // reuse squat ID as placeholder
        ['text' => 'Leg Extension – 3×12–15',          'exercise_id' => 20],
        ['text' => 'Calf Raise – 3×15–20',             'exercise_id' => 22],
      ],
    ],
  ],

  3 => [
    'name'        => 'Strength Focus (3 days)',
    'level'       => 'Intermediate',
    'goal'        => 'Strength',
    'duration'    => '8 weeks',
    'days'        => 3,
    'description' => 'Priority on heavy squat, bench and deadlift with accessory volume.',
    'schedule'    => [
      'Day 1 – Squat Focus' => [
        ['text' => 'Back Squat – 5×5',                 'exercise_id' => 17],
        ['text' => 'Leg Press – 3×8',                  'exercise_id' => 18],
        ['text' => 'Leg Curl – 3×10–12',               'exercise_id' => 21],
        ['text' => 'Standing Calf Raise – 3×12–15',    'exercise_id' => 22],
      ],
      'Day 2 – Bench Focus' => [
        ['text' => 'Barbell Bench Press – 5×5',        'exercise_id' => 2],
        ['text' => 'Incline Dumbbell Press – 3×8–10',  'exercise_id' => 3],
        ['text' => 'Seated Cable Row – 3×8–10',        'exercise_id' => 9],
        ['text' => 'Dumbbell Lateral Raise – 3×12–15', 'exercise_id' => 14],
      ],
      'Day 3 – Deadlift Focus' => [
        ['text' => 'Romanian Deadlift – 5×5',          'exercise_id' => 19],
        ['text' => 'Pull-ups or Lat Pulldown – 3×8–10','exercise_id' => 8],
        ['text' => 'Barbell Row – 3×8–10',             'exercise_id' => 11],
        ['text' => 'Plank – 3×45–60 sec',              'exercise_id' => 27],
      ],
    ],
  ],

  4 => [
    'name'        => 'Fat Loss + Conditioning (4 days)',
    'level'       => 'Beginner',
    'goal'        => 'Fat loss',
    'duration'    => '6 weeks',
    'days'        => 4,
    'description' => 'Resistance training + light conditioning to burn fat while keeping muscle.',
    'schedule'    => [
      'Day 1 – Upper' => [
        ['text' => 'Push-up or Bench Press – 3×10–12', 'exercise_id' => 1],
        ['text' => 'Lat Pulldown – 3×10–12',           'exercise_id' => 7],
        ['text' => 'Dumbbell Shoulder Press – 3×12',   'exercise_id' => 13],
        ['text' => 'Triceps Pushdown – 3×12–15',       'exercise_id' => 25],
        '10–15 min brisk walking or easy bike',
      ],
      'Day 2 – Lower' => [
        ['text' => 'Leg Press or Squat – 3×10–12',     'exercise_id' => 18],
        ['text' => 'Romanian Deadlift – 3×10–12',      'exercise_id' => 19],
        ['text' => 'Lunges – 3×10 each leg',           'exercise_id' => 17],
        ['text' => 'Plank – 3×30–45 sec',              'exercise_id' => 27],
        '10–15 min incline treadmill walk',
      ],
      'Day 3 – Upper 2' => [
        ['text' => 'Incline Dumbbell Press – 3×10–12', 'exercise_id' => 3],
        ['text' => 'Seated Cable Row – 3×10–12',       'exercise_id' => 9],
        ['text' => 'Lateral Raise – 3×15',             'exercise_id' => 14],
        ['text' => 'Hammer Curl – 3×12–15',            'exercise_id' => 24],
        '10–15 min step machine or bike',
      ],
      'Day 4 – Lower 2' => [
        ['text' => 'Leg Press – 3×12–15',              'exercise_id' => 18],
        ['text' => 'Leg Curl – 3×12–15',               'exercise_id' => 21],
        ['text' => 'Calf Raise – 3×15–20',             'exercise_id' => 22],
        ['text' => 'Russian Twist – 3×20',             'exercise_id' => 30],
        '10–15 min easy cardio of choice',
      ],
    ],
  ],

  5 => [
    'name'        => 'Home Bodyweight (3 days)',
    'level'       => 'Beginner',
    'goal'        => 'General fitness',
    'duration'    => '6 weeks',
    'days'        => 3,
    'description' => 'No-equipment routine you can do at home for strength, mobility and conditioning.',
    'schedule'    => [
      'Day 1 – Push Focus' => [
        ['text' => 'Push-ups – 4×AMRAP',               'exercise_id' => 1],
        'Chair Dips – 3×10–15',
        'Pike Push-ups – 3×8–10',
        ['text' => 'Plank – 3×30–45 sec',              'exercise_id' => 27],
      ],
      'Day 2 – Legs + Core' => [
        'Bodyweight Squats – 4×15–20',
        'Reverse Lunges – 3×12 each leg',
        'Glute Bridge – 3×15',
        ['text' => 'Crunches – 3×20',                  'exercise_id' => 28],
      ],
      'Day 3 – Pull + Full Body' => [
        'Inverted Rows – 4×AMRAP',
        'Superman Holds – 3×15–20 sec',
        'Mountain Climbers – 3×30 sec',
        'Side Plank – 3×20–30 sec each side',
      ],
    ],
  ],

  6 => [
    'name'        => 'Push / Pull / Legs (6 days)',
    'level'       => 'Advanced',
    'goal'        => 'Muscle gain',
    'duration'    => '8 weeks',
    'days'        => 6,
    'description' => 'High-volume split for advanced lifters with good recovery and nutrition.',
    'schedule'    => [
      'Day 1 – Push 1' => [
        ['text' => 'Barbell Bench Press – 4×6–8',      'exercise_id' => 2],
        ['text' => 'Incline Dumbbell Press – 4×8–10',  'exercise_id' => 3],
        ['text' => 'Overhead Press – 3×8–10',          'exercise_id' => 13],
        ['text' => 'Lateral Raise – 3×15',             'exercise_id' => 14],
        ['text' => 'Triceps Pushdown – 3×12–15',       'exercise_id' => 25],
      ],
      'Day 2 – Pull 1' => [
        ['text' => 'Pull-ups – 4×AMRAP',               'exercise_id' => 8],
        ['text' => 'Barbell Row – 4×8–10',             'exercise_id' => 11],
        ['text' => 'Seated Cable Row – 3×10–12',       'exercise_id' => 9],
        ['text' => 'Face Pull – 3×15',                 'exercise_id' => 12],
        ['text' => 'Hammer Curl – 3×10–12',            'exercise_id' => 24],
      ],
      'Day 3 – Legs 1' => [
        ['text' => 'Back Squat – 4×6–8',               'exercise_id' => 17],
        ['text' => 'Romanian Deadlift – 4×8–10',       'exercise_id' => 19],
        ['text' => 'Leg Press – 3×10–12',              'exercise_id' => 18],
        ['text' => 'Leg Curl – 3×12–15',               'exercise_id' => 21],
        ['text' => 'Calf Raise – 4×12–15',             'exercise_id' => 22],
      ],
      'Day 4 – Push 2' => [
        ['text' => 'Incline Bench Press – 4×8–10',     'exercise_id' => 3],
        ['text' => 'Machine Chest Press – 3×10–12',    'exercise_id' => 6],
        ['text' => 'Dumbbell Shoulder Press – 3×10–12','exercise_id' => 13],
        ['text' => 'Cable Crossover – 3×12–15',        'exercise_id' => 5],
        ['text' => 'Overhead Triceps Extension – 3×10–12', 'exercise_id' => 26],
      ],
      'Day 5 – Pull 2' => [
        ['text' => 'Lat Pulldown – 4×8–10',            'exercise_id' => 7],
        ['text' => 'One-Arm Dumbbell Row – 3×10–12',   'exercise_id' => 10],
        ['text' => 'Face Pull – 3×15',                 'exercise_id' => 12],
        ['text' => 'Barbell Curl – 3×10–12',           'exercise_id' => 23],
        ['text' => 'Cable Curl – 3×12–15',             'exercise_id' => 23],
      ],
      'Day 6 – Legs 2' => [
        ['text' => 'Leg Press or Front Squat – 4×8–10','exercise_id' => 18],
        ['text' => 'Romanian Deadlift – 3×8–10',       'exercise_id' => 19],
        ['text' => 'Walking Lunges – 3×12 each leg',   'exercise_id' => 17],
        ['text' => 'Leg Extension – 3×12–15',          'exercise_id' => 20],
        ['text' => 'Calf Raise – 4×15–20',             'exercise_id' => 22],
      ],
    ],
  ],
];
