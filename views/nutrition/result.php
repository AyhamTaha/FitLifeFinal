<?php
require_once __DIR__ . '/../../includes/security.php';

fitlife_start_session();

// basic validation + sanitization
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . fitlife_url('views/nutrition/calculator.php'));
    exit;
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);

require_once __DIR__ . '/../auth/dbconn.php';

$weight   = floatval($_POST['weight'] ?? 0);
$height   = floatval($_POST['height'] ?? 0);
$age      = intval($_POST['age'] ?? 0);
$gender   = $_POST['gender'] ?? 'male';
$goal     = strtolower($_POST['goal'] ?? 'maintain');
$activity = $_POST['activity'] ?? 'Moderate';

// Server-side checks mirror the form limits and constrain every choice.
if ($weight < 30 || $weight > 300
    || $height < 100 || $height > 250
    || $age < 10 || $age > 100
    || !in_array($gender, ['male', 'female'], true)
    || !in_array($goal, ['bulk', 'cut', 'maintain'], true)
    || !array_key_exists($activity, [
        'Sedentary' => true,
        'Light' => true,
        'Moderate' => true,
        'Active' => true,
        'Veryactive' => true,
    ])) {
    http_response_code(422);
    echo 'Invalid input. <a href="' . fitlife_escape(fitlife_url('views/nutrition/calculator.php')) . '">Go back</a>';
    exit;
}

// BMR (Mifflin-St Jeor)
if ($gender === 'male') {
    $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
} else {
    $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
}

// activity multipliers
$activityLevels = [
    "Sedentary"  => 1.2,
    "Light"      => 1.375,
    "Moderate"   => 1.55,
    "Active"     => 1.725,
    "Veryactive" => 1.9
];
$mult     = $activityLevels[$activity] ?? 1.55;
$calories = $bmr * $mult;

// goal adjustment
if ($goal === 'bulk') $calories += 300;
if ($goal === 'cut')  $calories -= 300;

// macros
$protein    = round($weight * 1.8, 1);
$fatPercent = ($goal === 'cut') ? 0.25 : 0.27;
$fats       = round(($calories * $fatPercent) / 9, 1);
$carbs      = round(($calories - ($protein*4 + $fats*9)) / 4, 1);

// Save the estimate, but keep showing the calculated result if saving fails.
$saveWarning = '';
try {
    $storedGender = ucfirst(strtolower($gender));
    $stmt = $conn->prepare(
        'INSERT INTO user_results (weight, height, age, gender, goal, calories, protein_g, carbs_g, fats_g)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'ddissdddd',
        $weight,
        $height,
        $age,
        $storedGender,
        $goal,
        $calories,
        $protein,
        $carbs,
        $fats
    );
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife nutrition result save failed: ' . $exception->getMessage());
    $saveWarning = 'Your nutrition plan was calculated, but it could not be saved. You can still use the results below.';
}

// BMI
$bmi        = $weight / (($height / 100) ** 2);
$bmi_status = 'Normal';
if ($bmi < 18.5)      $bmi_status = 'Underweight';
elseif ($bmi >= 25)   $bmi_status = 'Overweight / Obese';

// water
$water_l = round($weight * 0.033, 2);

$mealPlans = [
    'bulk' => [
        'breakfast' => [
            ['title'=>'Oats bowl','desc'=>'80g oats + milk + banana + 1 tbsp peanut butter','protein'=>25,'kcal'=>550],
            ['title'=>'Eggs & toast','desc'=>'4 eggs + 2 toast + cheese','protein'=>36,'kcal'=>620]
        ],
        'lunch' => [
            ['title'=>'Rice & chicken','desc'=>'200g rice + 200g chicken + veg','protein'=>55,'kcal'=>750],
            ['title'=>'Pasta & tuna','desc'=>'Pasta + tuna + olive oil','protein'=>45,'kcal'=>700]
        ],
        'dinner' => [
            ['title'=>'Salmon & potato','desc'=>'150g salmon + potato + salad','protein'=>40,'kcal'=>700]
        ],
        'snacks' => [
            ['title'=>'Greek yogurt','protein'=>10,'kcal'=>120],
            ['title'=>'Nuts & dried fruit','protein'=>8,'kcal'=>200],
            ['title'=>'Protein shake','protein'=>25,'kcal'=>150]
        ]
    ],
    'cut' => [
        'breakfast' => [
            ['title'=>'Veggie omelette','desc'=>'3 eggs + spinach + tomatoes','protein'=>28,'kcal'=>320],
            ['title'=>'Small oats','desc'=>'40g oats + water + berries','protein'=>10,'kcal'=>220]
        ],
        'lunch' => [
            ['title'=>'Tuna salad','desc'=>'Tuna + mixed greens + olive oil','protein'=>35,'kcal'=>380],
            ['title'=>'Grilled chicken & veg','desc'=>'150g chicken + large salad','protein'=>45,'kcal'=>420]
        ],
        'dinner' => [
            ['title'=>'Soup + lean protein','desc'=>'Vegetable soup + chicken','protein'=>30,'kcal'=>300]
        ],
        'snacks' => [
            ['title'=>'Apple','protein'=>0,'kcal'=>80],
            ['title'=>'Cucumber + labneh','protein'=>5,'kcal'=>60],
            ['title'=>'10 almonds','protein'=>3,'kcal'=>70]
        ]
    ],
    'maintain' => [
        'breakfast' => [
            ['title'=>'Medium oats','desc'=>'60g oats + milk + fruit','protein'=>15,'kcal'=>350],
            ['title'=>'Egg sandwich','desc'=>'2 eggs + wholegrain bread','protein'=>20,'kcal'=>380]
        ],
        'lunch' => [
            ['title'=>'Rice + chicken','desc'=>'100–150g rice + 150g chicken','protein'=>40,'kcal'=>550],
            ['title'=>'Pasta & veg','desc'=>'Pasta + veg + light sauce','protein'=>30,'kcal'=>500]
        ],
        'dinner' => [
            ['title'=>'Tuna salad','desc'=>'Tuna + salad + olive oil','protein'=>30,'kcal'=>420]
        ],
        'snacks' => [
            ['title'=>'Mixed nuts','protein'=>5,'kcal'=>150],
            ['title'=>'Fruit','protein'=>1,'kcal'=>80],
            ['title'=>'Yogurt','protein'=>10,'kcal'=>120]
        ]
    ]
];

$plan = $mealPlans[$goal];
include __DIR__ . '/../templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FitLife — Nutrition Results</title>
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/stylenut.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="theme-light">

<div class="topbar">
    <div class="topbar-left">
        <h1>FitLife — Your Nutrition Plan</h1>
        <p class="topbar-sub">Based on the data you entered in the calculator.</p>
        <?php if ($saveWarning !== ''): ?>
            <p class="error-msg"><?= htmlspecialchars($saveWarning) ?></p>
        <?php endif; ?>
    </div>
    <div class="theme-toggle">
        <label class="switch">
            <input id="themeSwitch" type="checkbox">
            <span class="slider round"></span>
        </label>
        <span class="small">Pro Theme</span>
    </div>
</div>

<main class="results-wrap" id="printArea">

    <!-- Stats -->
    <section class="row-cards">
        <div class="card stat">
            <h3>Daily Calories</h3>
            <p class="big"><?= round($calories) ?> kcal</p>
            <p class="muted">
                BMR: <?= round($bmr) ?> kcal — Activity: <?= htmlspecialchars($activity) ?> (×<?= $mult ?>)
            </p>
        </div>
        <div class="card stat">
            <h3>BMI</h3>
            <p class="big"><?= round($bmi,1) ?></p>
            <p class="muted"><?= $bmi_status ?></p>
        </div>
        <div class="card stat">
            <h3>Water Target</h3>
            <p class="big"><?= $water_l ?> L</p>
            <p class="muted"><?= ceil($water_l*4) ?> cups (approx.)</p>
        </div>
    </section>

    <!-- Macros -->
    <section class="macros-card card">
        <h3>Daily Macros</h3>
        <div class="macros-row">
            <?php foreach (['Protein'=>$protein,'Carbs'=>$carbs,'Fats'=>$fats] as $label=>$val): ?>
                <div class="macro-box">
                    <h4><?= $label ?></h4>
                    <p class="big"><?= $val ?> g</p>
                    <?php
                        if ($label === 'Fats') {
                            $percent = ($fats * 9 / $calories) * 100;
                        } else {
                            $percent = ($val * 4 / $calories) * 100;
                        }
                        $percent = min(100, max(0, $percent));
                    ?>
                    <div class="progress">
                        <div style="width:<?= $percent ?>%"></div>
                    </div>
                    <p class="muted"><?= round($percent) ?>% of calories</p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="macro-chart-wrap">
            <canvas id="macroChart"></canvas>
        </div>
    </section>

    <!-- Meal Plan -->
    <section class="mealplan card">
        <h3>Suggested Meal Plan (<?= ucfirst($goal) ?>)</h3>
        <div class="meals-grid">
            <?php foreach (['breakfast','lunch','dinner','snacks'] as $mealType): ?>
                <div class="meal-box">
                    <h4><?= ucfirst($mealType) ?></h4>
                    <?php if ($mealType === 'snacks'): ?>
                        <ul>
                            <?php foreach ($plan['snacks'] as $sn): ?>
                                <li>
                                    <?= htmlspecialchars($sn['title']) ?>
                                    — <?= $sn['protein'] ?>g Protein ~<?= $sn['kcal'] ?> kcal
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?php foreach ($plan[$mealType] as $item): ?>
                            <div class="meal-item">
                                <strong><?= htmlspecialchars($item['title']) ?></strong>
                                <p class="muted"><?= htmlspecialchars($item['desc']) ?></p>
                                <small class="muted">
                                    Protein: <?= $item['protein'] ?>g — ~<?= $item['kcal'] ?> kcal
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Foods Rich in each Macro -->
    <section class="card">
        <h3>Foods Rich in Each Macro</h3>
        <p class="muted">Click a macro to see example foods from our small database.</p>
        <div class="macro-boxes" id="macroBoxes">
            <div class="macro-box macro-pill" data-macro="protein">Protein</div>
            <div class="macro-box macro-pill" data-macro="carbs">Carbohydrates</div>
            <div class="macro-box macro-pill" data-macro="fats">Fats</div>
        </div>
        <div id="food-list" class="food-grid"></div>
    </section>

    <!-- Supplements -->
    <section class="supplements card">
        <h3>Suggested Supplements (Optional)</h3>
        <ul>
            <li>Whey protein to help you reach your daily protein target</li>
            <li>Creatine monohydrate (5g/day)</li>
            <li>Omega-3 (fish oil)</li>
            <li>Basic multivitamin if your diet is limited</li>
        </ul>
        <p class="muted">
            Always check with a doctor if you have kidney, liver or heart issues
            before using supplements.
        </p>
    </section>

    <!-- Actions -->
    <div class="result-actions">
        <button id="downloadPdf" class="btn primary">Download PDF</button>
        <a class="btn ghost" href="calculator.php">Recalculate</a>
    </div>
</main>

<script>
// Theme toggle
const switchEl = document.getElementById('themeSwitch');
const body = document.body;
const saved = localStorage.getItem('fitlife_theme');
if (saved === 'pro') {
    body.classList.remove('theme-light');
    body.classList.add('theme-pro');
    switchEl.checked = true;
}
switchEl.addEventListener('change', e => {
    if (e.target.checked) {
        body.classList.remove('theme-light');
        body.classList.add('theme-pro');
        localStorage.setItem('fitlife_theme','pro');
    } else {
        body.classList.remove('theme-pro');
        body.classList.add('theme-light');
        localStorage.setItem('fitlife_theme','light');
    }
});

// Chart.js doughnut macro chart
const ctx = document.getElementById('macroChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Protein (kcal)', 'Carbs (kcal)', 'Fats (kcal)'],
        datasets: [{
            data: [<?= round($protein*4) ?>, <?= round($carbs*4) ?>, <?= round($fats*9) ?>],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// PDF Download
document.getElementById('downloadPdf').addEventListener('click', async () => {
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
    document.body.appendChild(script);
    script.onload = async () => {
        const el = document.getElementById('printArea');
        const canvas = await html2canvas(el, { scale: 1.5 });
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: [canvas.width, canvas.height]
        });
        pdf.addImage(imgData,'PNG',0,0,canvas.width,canvas.height);
        pdf.save('fitlife-results.pdf');
    };
});

// Foods by macro (using apifood.php)
const macroBoxes = document.querySelectorAll('#macroBoxes .macro-pill');
const foodList = document.getElementById('food-list');

macroBoxes.forEach(box => {
    box.addEventListener('click', async () => {
        const macro = box.dataset.macro;

        if (box.classList.contains('active')) {
            box.classList.remove('active');
            foodList.innerHTML = '';
            return;
        }

        macroBoxes.forEach(b => b.classList.remove('active'));
        box.classList.add('active');

        try {
            const res = await fetch(`apifood.php?macro=${macro}&limit=6`);
            const data = await res.json();

            foodList.innerHTML = '';

            if (!Array.isArray(data) || !data.length) {
                foodList.innerHTML = '<p class="muted">No foods found.</p>';
                return;
            }

            data.forEach(food => {
                const div = document.createElement('div');
                div.classList.add('meal-item', 'food-row');
                div.innerHTML = `
                    <strong>${food.name}</strong>
                    <span>
                        Protein: ${food.protein_g}g  •
                        Carbs: ${food.carbohydrates_g}g  •
                        Fats: ${food.fat_g}g  •
                        ${food.calories} kcal
                    </span>
                `;
                foodList.appendChild(div);
            });

        } catch (err) {
            console.error(err);
            foodList.innerHTML = '<p class="muted">Error loading foods.</p>';
        }
    });
});
</script>

</body>
</html>
<?php include __DIR__ . '/../templates/footer.php'; ?>
