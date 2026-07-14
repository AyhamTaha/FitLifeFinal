<?php
header("Content-Type: application/json; charset=UTF-8");

$jsonPath = __DIR__ . "/foods.json";


if (!file_exists($jsonPath)) {
    echo json_encode(["error" => "Foods database not found."]);
    exit;
}

$all = json_decode(file_get_contents($jsonPath), true);
if (!is_array($all)) {
    echo json_encode(["error" => "Invalid foods database."]);
    exit;
}


$macro = strtolower($_GET['macro'] ?? 'protein'); // protein|carbs|fats
$limit = intval($_GET['limit'] ?? 6);


$macroKey = 'protein_g';
if ($macro === 'carbs') $macroKey = 'carbohydrates_g';
elseif ($macro === 'fats') $macroKey = 'fat_g';


usort($all, function($a, $b) use ($macroKey) {
    $va = floatval($a[$macroKey] ?? 0);
    $vb = floatval($b[$macroKey] ?? 0);
    return $vb <=> $va;
});


$results = array_slice($all, 0, max(1, $limit));


$out = [];
foreach ($results as $item) {
    $out[] = [
        "name" => $item['name'],
        "calories" => is_numeric($item['calories']) ? (float)$item['calories'] : $item['calories'],
        "protein_g" => is_numeric($item['protein_g']) ? (float)$item['protein_g'] : $item['protein_g'],
        "carbohydrates_g" => is_numeric($item['carbohydrates_g']) ? (float)$item['carbohydrates_g'] : $item['carbohydrates_g'],
        "fat_g" => is_numeric($item['fat_g']) ? (float)$item['fat_g'] : $item['fat_g']
    ];
}

echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
