<?php
// scripts/import_program_schedule.php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo 'This maintenance script can only be run from the command line.';
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../views/auth/dbconn.php';          // gives $conn (MySQLi)
require_once __DIR__ . '/../views/programs/program-data.php'; // gives $programs array

// Safety check
if (!isset($programs) || !is_array($programs)) {
    die('No $programs array found in program-data.php');
}

$existingDays = (int)$conn->query('SELECT COUNT(*) AS total FROM program_days')->fetch_assoc()['total'];
if ($existingDays > 0) {
    echo "Program schedules already exist; no import was performed.\n";
    exit;
}

$conn->begin_transaction();

try {

foreach ($programs as $programId => $prog) {
    if (!isset($prog['schedule']) || !is_array($prog['schedule'])) {
        continue;
    }

    $dayOrder = 1;

    foreach ($prog['schedule'] as $dayTitle => $exerciseList) {
        // Insert day
        $stmtDay = $conn->prepare("
            INSERT INTO program_days (program_id, day_title, day_order, title)
            VALUES (?, ?, ?, ?)
        ");
        $stmtDay->bind_param('isis', $programId, $dayTitle, $dayOrder, $dayTitle);
        $stmtDay->execute();
        $dayId = $stmtDay->insert_id;
        $stmtDay->close();

        // Insert exercises for that day
        $sortOrder = 1;
        foreach ($exerciseList as $item) {
            if (is_array($item) && isset($item['exercise_id'])) {
                $text       = $item['text'];
                $exerciseId = $item['exercise_id']; // int (can be null if you ever want)
            } else {
                // pure text step (cardio etc.)
                $text       = is_array($item) ? ($item['text'] ?? '') : $item;
                $exerciseId = null;
            }

            $stmtEx = $conn->prepare("
                INSERT INTO program_exercises (program_day_id, exercise_id, display_text, sort_order)
                VALUES (?, ?, ?, ?)
            ");
            $stmtEx->bind_param('iisi', $dayId, $exerciseId, $text, $sortOrder);
            $stmtEx->execute();
            $stmtEx->close();

            $sortOrder++;
        }

        $dayOrder++;
    }
}

$conn->commit();
echo "Import finished. Check program_days and program_exercises tables.\n";
} catch (Throwable $exception) {
    $conn->rollback();
    error_log('FitLife program schedule import failed: ' . $exception->getMessage());
    fwrite(STDERR, "Import failed; all changes were rolled back.\n");
    exit(1);
}
