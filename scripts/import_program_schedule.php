<?php
// scripts/import_program_schedule.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';                 // gives $conn (MySQLi)
require_once __DIR__ . '/../views/programs/program-data.php'; // gives $programs array

// Safety check
if (!isset($programs) || !is_array($programs)) {
    die('No $programs array found in program-data.php');
}

foreach ($programs as $programId => $prog) {
    if (!isset($prog['schedule']) || !is_array($prog['schedule'])) {
        continue;
    }

    $dayOrder = 1;

    foreach ($prog['schedule'] as $dayTitle => $exerciseList) {
        // Insert day
        $stmtDay = $conn->prepare("
            INSERT INTO program_days (program_id, day_title, day_order)
            VALUES (?, ?, ?)
        ");
        $stmtDay->bind_param('isi', $programId, $dayTitle, $dayOrder);
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

echo "✅ Import finished. Check program_days and program_exercises tables.";
