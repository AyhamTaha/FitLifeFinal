<?php
// views/programs/program.php
include __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../auth/dbconn.php';

$programId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT id, name, level, goal, days_per_week, duration_weeks, description
        FROM programs
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $programId);
$stmt->execute();
$result = $stmt->get_result();
$program = $result->fetch_assoc();
$stmt->close();
?>
<link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/style.css">

<section class="container">
  <nav class="breadcrumbs">
    <a href="<?= $fitlifeBasePath ?>/views/auth/home.php">Home</a> ›
    <a href="<?= $fitlifeBasePath ?>/views/programs/programs.php">Programs</a> ›
    <span><?php echo $program ? htmlspecialchars($program['name']) : 'Program'; ?></span>
  </nav>

  <?php if (!$program): ?>
    <p>Program not found.</p>
  <?php else: ?>

    <header class="program-header">
    <div class="prog">  
    <h2><?php echo htmlspecialchars($program['name']); ?></h2>
      <p><?php echo htmlspecialchars($program['description']); ?></p>
      <a href="programs.php" class="btn back-btn" style="width: 200px;">← Back to all programs</a>
  </div>
      <ul class="program-meta">
        <li><strong>Level:</strong> <span class="meta-value"> <?php echo htmlspecialchars($program['level']); ?>  </span> </li>
        <li><strong>Goal:</strong> <span class="meta-value"> <?php echo htmlspecialchars($program['goal']); ?>  </span> </li>
        <li><strong>Days/week:</strong> <span class="meta-value"> <?php echo (int)$program['days_per_week']; ?>  </span> </li>
        <li><strong>Duration:</strong> <span class="meta-value"> <?php echo (int)$program['duration_weeks']; ?> weeks  </span></li>
      </ul>

     
    </header>

    <?php
    $days = [];
    $sqlDays = "SELECT id, day_title, day_order
                FROM program_days
                WHERE program_id = ?
                ORDER BY day_order";
    $stmtDays = $conn->prepare($sqlDays);
    $stmtDays->bind_param('i', $programId);
    $stmtDays->execute();
    $resDays = $stmtDays->get_result();
    while ($row = $resDays->fetch_assoc()) {
        $days[] = $row;
    }
    $stmtDays->close();
    ?>

    <section class="program-schedule">
      <?php if (empty($days)): ?>
        <p>No schedule defined yet for this program.</p>
      <?php else: ?>
        <?php foreach ($days as $day): ?>
          <?php
          $dayId = (int)$day['id'];
          $dayExercises = [];

          $sqlEx = "SELECT pe.display_text,
                           pe.exercise_id,
                           e.name AS exercise_name
                    FROM program_exercises pe
                    LEFT JOIN exercises e ON pe.exercise_id = e.id
                    WHERE pe.program_day_id = ?
                    ORDER BY pe.sort_order";
          $stmtEx = $conn->prepare($sqlEx);
          $stmtEx->bind_param('i', $dayId);
          $stmtEx->execute();
          $resEx = $stmtEx->get_result();
          while ($rowEx = $resEx->fetch_assoc()) {
              $dayExercises[] = $rowEx;
          }
          $stmtEx->close();
          ?>

          <article class="program-day">
            <h3><?php echo htmlspecialchars($day['day_title']); ?></h3>

            <?php if (empty($dayExercises)): ?>
              <p>No exercises added for this day yet.</p>
            <?php else: ?>
              <ul>
                <?php foreach ($dayExercises as $item): ?>
                  <?php
                    $text       = $item['display_text'];
                    $exerciseId = $item['exercise_id'];
                  ?>
                  <?php if ($exerciseId): ?>
                    <?php $url = fitlife_url('views/workouts/exercise.php?id=' . (int)$exerciseId); ?>
                    <li>
                      <a href="<?php echo $url; ?>">
                        <?php echo htmlspecialchars($text ?: $item['exercise_name']); ?>
                      </a>
                    </li>
                  <?php else: ?>
                    <li><?php echo htmlspecialchars($text); ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

  <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>


