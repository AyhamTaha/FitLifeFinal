<?php
// views/programs/programs.php

include __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../auth/dbconn.php';

// Fetch all programs using MySQLi ($conn)
$programs = [];

$sql = "SELECT id, name, level, goal, days_per_week, duration_weeks, description
        FROM programs
        ORDER BY id";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
    $result->free();
}

$goalMap = [
    'Muscle gain'      => 'muscle',
    'Fat loss'         => 'fat-loss',
    'Strength'         => 'strength',
    'General fitness'  => 'general',
];
?>
<link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/style.css">

<section class="container">
  <nav class="breadcrumbs">
    <a href="<?= $fitlifeBasePath ?>/views/auth/home.php">Home</a> ›
    <span>Programs</span>
  </nav>

  <header class="workout-header">
    <h2 style=" color:#ff6600;">Training Programs</h2>
    <p>Choose a program that matches your goal and level.</p>

    <div class="program-filters">
      <input 
        type="text" 
        id="programSearch" 
        placeholder="Search programs by name or description..."
      >

      <select id="goalFilter">
        <option value="">All goals</option>
        <option value="muscle">Muscle gain</option>
        <option value="fat-loss">Fat loss</option>
        <option value="strength">Strength</option>
        <option value="general">General fitness</option>
      </select>

      <select id="levelFilter">
        <option value="">All levels</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>
  </header>

  <section class="program-grid" id="programGrid">
    <?php foreach ($programs as $program): ?>
      <?php
        $goalText  = $program['goal'];
        $goalKey   = $goalMap[$goalText] ?? '';
        $levelKey  = strtolower($program['level']);
      ?>
      <article class="program-card"
               data-goal="<?php echo htmlspecialchars($goalKey); ?>"
               data-level="<?php echo htmlspecialchars($levelKey); ?>"
               data-days="<?php echo (int)$program['days_per_week']; ?>"
               data-duration="<?php echo (int)$program['duration_weeks']; ?>">

        <div class="program-card-body">
          <h3><?php echo htmlspecialchars($program['name']); ?></h3>
          <p class="program-description">
            <?php echo htmlspecialchars($program['description']); ?>
          </p>

          <ul class="program-meta">
            <li><strong>Level:</strong> <?php echo htmlspecialchars($program['level']); ?></li>
            <li><strong>Goal:</strong> <?php echo htmlspecialchars($program['goal']); ?></li>
            <li><strong>Days/week:</strong> <?php echo (int)$program['days_per_week']; ?></li>
            <li><strong>Duration:</strong> <?php echo (int)$program['duration_weeks']; ?> weeks</li>
          </ul>

          <a href="program.php?id=<?php echo (int)$program['id']; ?>" class="btn" style="color: white;">
            View program
          </a>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
