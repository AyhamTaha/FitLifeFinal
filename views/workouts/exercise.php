<?php
// views/workouts/exercise.php

include __DIR__ . '/../templates/header.php';
require __DIR__ . '/../auth/dbconn.php';          // DB connection
require __DIR__ . '/../programs/program-data.php'; // for where this exercise appears

// 1) Get exercise id from URL, e.g. exercise.php?id=2
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo '<section class="container exercise-details"><p>Invalid exercise.</p></section>';
    include __DIR__ . '/../templates/footer.php';
    exit;
}

// 2) Fetch exercise from DB
$stmt = $conn->prepare("SELECT * FROM exercises WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result   = $stmt->get_result();
$exercise = $result->fetch_assoc();
$stmt->close();

if (!$exercise) {
    echo '<section class="container exercise-details"><p>Exercise not found.</p></section>';
    include __DIR__ . '/../templates/footer.php';
    exit;
}

// 3) Image path handling (if DB has only filename)
$imagePath = $exercise['image'] ?? '';
if ($imagePath !== '' && $imagePath[0] !== '/') {
    $imagePath = fitlife_url('public/images/' . $imagePath);
}

// 4) Difficulty CSS class
$difficultyClass = 'badge-' . strtolower($exercise['difficulty'] ?? '');

// 5) Map muscle_id -> title for breadcrumbs/back button
$exerciseToMuscleId = [
  1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1,        // Chest
  7 => 2, 8 => 2, 9 => 2, 10 => 2, 11 => 2, 12 => 2,     // Back
  13 => 3, 14 => 3, 15 => 3, 16 => 3,                    // Shoulders
  17 => 4, 18 => 4, 19 => 4, 20 => 4, 21 => 4, 22 => 4,  // Legs
  23 => 5, 24 => 5, 25 => 5, 26 => 5,                    // Arms
  27 => 6, 28 => 6, 29 => 6, 30 => 6                     // Abs
];

$muscleTitles = [
  1 => 'Chest',
  2 => 'Back',
  3 => 'Shoulders',
  4 => 'Legs',
  5 => 'Arms',
  6 => 'Abs',
];

$muscleIdForThisExercise    = $exerciseToMuscleId[$id] ?? ($exercise['muscle_id'] ?? 1);
$muscleTitleForThisExercise = $muscleTitles[$muscleIdForThisExercise] ?? 'Muscle';

// 6) Where this exercise is used in programs (from program-data.php)
$usedInPrograms = [];

if (!empty($programs)) {
    foreach ($programs as $programId => $program) {
        if (!isset($program['schedule'])) continue;

        foreach ($program['schedule'] as $dayTitle => $exerciseList) {
            foreach ($exerciseList as $item) {
                if (is_array($item) && isset($item['exercise_id'])) {
                    if ((int)$item['exercise_id'] === $id) {
                        $usedInPrograms[] = [
                            'program_id'   => $programId,
                            'program_name' => $program['name'],
                            'day_title'    => $dayTitle,
                        ];
                    }
                }
            }
        }
    }
}
?>
<link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/style.css">

<section class="container exercise-details">

  <!-- BREADCRUMBS -->
  <nav class="breadcrumbs">
    <a href="<?= $fitlifeBasePath ?>/views/auth/home.php">Home</a> ›
    <a href="<?= $fitlifeBasePath ?>/views/workouts/muscles.php">Workout Library</a> ›
    <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=<?php echo $muscleIdForThisExercise; ?>">
      <?php echo htmlspecialchars($muscleTitleForThisExercise); ?> Exercises
    </a> ›
    <span><?php echo htmlspecialchars($exercise['name']); ?></span>
  </nav>

  <article>
    <header class="exercise-head">
      <h2><?php echo htmlspecialchars($exercise['name']); ?></h2>
      <?php if (!empty($exercise['difficulty'])): ?>
        <span class="badge <?php echo htmlspecialchars($difficultyClass); ?>">
          <?php echo htmlspecialchars($exercise['difficulty']); ?>
        </span>
      <?php endif; ?>
    </header>

    <!-- BACK BUTTON -->
    <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=<?php echo $muscleIdForThisExercise; ?>"
       class="btn back-btn">
      ← Back to <?php echo htmlspecialchars($muscleTitleForThisExercise); ?> exercises
    </a>

    <div class="exercise-layout">
      <div class="exercise-media">
        <?php if (!empty($imagePath)): ?>
          <img src="<?php echo htmlspecialchars($imagePath); ?>"
               alt="<?php echo htmlspecialchars($exercise['name']); ?>">
        <?php endif; ?>

        <?php if (!empty($exercise['video_url'])): ?>
          <div class="video-wrapper">
            <iframe
              src="<?php echo htmlspecialchars($exercise['video_url']); ?>"
              frameborder="0"
              allowfullscreen>
            </iframe>
          </div>
        <?php endif; ?>
      </div>

      <div class="exercise-info">
        <h3>Description</h3>
        <p><?php echo htmlspecialchars($exercise['description'] ?? ''); ?></p>

        <ul class="exercise-meta">
          <?php if (!empty($exercise['main_muscle'])): ?>
            <li><strong>Main muscle:</strong> <?php echo htmlspecialchars($exercise['main_muscle']); ?></li>
          <?php endif; ?>

          <?php if (!empty($exercise['equipment'])): ?>
            <li><strong>Equipment:</strong> <?php echo htmlspecialchars($exercise['equipment']); ?></li>
          <?php endif; ?>

          <?php if (!empty($exercise['difficulty'])): ?>
            <li><strong>Difficulty:</strong> <?php echo htmlspecialchars($exercise['difficulty']); ?></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </article>

  <?php if (!empty($usedInPrograms)): ?>
    <section class="exercise-program-usage">
      <h3>Where this exercise appears</h3>
      <p>This exercise is used in the following programs:</p>
      <ul>
        <?php foreach ($usedInPrograms as $usage): ?>
          <li>
            <a href="<?= $fitlifeBasePath ?>/views/programs/program.php?id=<?php echo (int)$usage['program_id']; ?>">
              <?php echo htmlspecialchars($usage['program_name']); ?>
            </a>
            – <?php echo htmlspecialchars($usage['day_title']); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
