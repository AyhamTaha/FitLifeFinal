<?php include __DIR__ . '/../templates/header.php'; ?>

<link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/style.css">

<section class="hero">
  <div class="container">
    <h2>Workout Library</h2>
    <p>Select a muscle group to explore exercises, images and videos.</p>
  </div>
</section>

<section class="container muscle-grid">
  <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=1" class="muscle-card">
    <h3>Chest</h3>
    <p>Push movements for upper body strength.</p>
  </a>

  <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=2" class="muscle-card">
    <h3>Back</h3>
    <p>Pulling exercises for lats and upper back.</p>
  </a>

  <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=3" class="muscle-card">
    <h3>Shoulders</h3>
    <p>Deltoid-focused movements.</p>
  </a>

  <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=4" class="muscle-card">
    <h3>Legs</h3>
    <p>Quads, hamstrings, and glutes.</p>
  </a>

  <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=5" class="muscle-card">
    <h3>Arms</h3>
    <p>Biceps and triceps isolation work.</p>
  </a>

  <a href="<?= $fitlifeBasePath ?>/views/workouts/exercises.php?muscle_id=6" class="muscle-card">
    <h3>Abs</h3>
    <p>Core stability and ab exercises.</p>
  </a>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
