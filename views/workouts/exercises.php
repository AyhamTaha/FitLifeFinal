 <?php
// views/workouts/exercises.php

include __DIR__ . '/../templates/header.php';
require __DIR__ . '/../auth/dbconn.php';   // DB connection

// which muscle to show? e.g. ?muscle_id=1
$muscleId = isset($_GET['muscle_id']) ? (int)$_GET['muscle_id'] : 1;

// labels for page titles / breadcrumbs
$muscleTitles = [
    1 => 'Chest',
    2 => 'Back',
    3 => 'Shoulders',
    4 => 'Legs',
    5 => 'Arms',
    6 => 'Abs',
];

$muscleName = $muscleTitles[$muscleId] ?? 'Muscle';

// get exercises for that muscle from DB
$stmt = $conn->prepare("SELECT * FROM exercises WHERE muscle_id = ? ORDER BY id");
$stmt->bind_param('i', $muscleId);
$stmt->execute();
$result    = $stmt->get_result();
$exercises = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/style.css">

<section class="container">
  <nav class="breadcrumbs">
    <a href="<?= $fitlifeBasePath ?>/views/auth/home.php">Home</a> ›
    <a href="<?= $fitlifeBasePath ?>/views/workouts/muscles.php">Workout Library</a> ›
    <span><?php echo htmlspecialchars($muscleName); ?></span>
  </nav>

  <header class="workout-header">
    <h2  style=" color:#ff6600;"><?php echo htmlspecialchars($muscleName); ?> Exercises</h2>
    <p>Choose an exercise to see full details and video.</p>

    <div class="workout-filters">
      <input type="text" id="exerciseSearch" placeholder="Search exercise...">

      <select id="difficultyFilter">
        <option value="">All difficulties</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
      </select>
    </div>

    <p id="exerciseStats" class="exercise-stats"></p>
  </header>

  <section class="exercise-grid" id="exerciseGrid">
    <?php if (empty($exercises)): ?>
      <p>No exercises available for this muscle yet.</p>
    <?php else: ?>
      <?php foreach ($exercises as $exercise): ?>
        <?php
          $difficultyClass = 'badge-' . strtolower($exercise['difficulty']); // e.g. badge-beginner
          $imgPath = fitlife_url('public/images/' . $exercise['image']);
        ?>
        <article class="exercise-card"
                 data-difficulty="<?php echo htmlspecialchars($exercise['difficulty']); ?>">
          <img src="<?php echo htmlspecialchars($imgPath); ?>"
               alt="<?php echo htmlspecialchars($exercise['name']); ?>">

          <div class="exercise-body">
            <h3><?php echo htmlspecialchars($exercise['name']); ?></h3>
            <p><?php echo htmlspecialchars($exercise['description']); ?></p>

            <span class="badge <?php echo htmlspecialchars($difficultyClass); ?>">
              <?php echo htmlspecialchars($exercise['difficulty']); ?>
            </span>

            <a href="exercise.php?id=<?php echo (int)$exercise['id']; ?>" class="btn">
              View details
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
