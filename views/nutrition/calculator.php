<?php   include __DIR__ . '/../templates/header.php';  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FitLife — Nutrition Calculator</title>
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/stylenut.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body class="theme-light">
<div class="topbar">
    <div class="topbar-left">
        <h1>FitLife — Nutrition Calculator</h1>
        <p class="topbar-sub">Aligned with your training goals from FitLife Home.</p>
    </div>
    <div class="theme-toggle">
        <label class="switch">
            <input id="themeSwitch" type="checkbox">
            <span class="slider round"></span>
        </label>
        <span class="small">Pro Theme</span>
    </div>
</div>

<main class="calc-wrap">

    <div class="calc-left">
        <!-- Intro card -->
        <section class="intro card">
            <p class="intro-tag">Smart Nutrition</p>
            <h2>Nutrition & Meal Planning</h2>
            <p>
                Enter your details to get your daily calories, macros, water intake,
                BMI and a suggested meal plan that fits your goal.
            </p>
            <ul class="intro-bullets">
                <li>Based on Mifflin-St Jeor equation</li>
                <li>Adjusts for activity level and goal (bulk / cut / maintain)</li>
                <li>Includes water target and simple meal ideas</li>
            </ul>

            <p class="trust-line">
                All formulas and example plans were reviewed by a certified dietitian
                and are meant for generally healthy adults.
            </p>
        </section>

        <!-- Help card -->
        <section class="help card">
            <h4>What this calculator uses</h4>
            <ul>
                <li>Mifflin-St Jeor BMR formula</li>
                <li>Standard activity multipliers to estimate TDEE</li>
                <li>Protein per kg of bodyweight</li>
                <li>Fats 20–30% of daily calories, carbs fill remaining</li>
            </ul>
        </section>
    </div>

    <form class="card form-card" method="POST" action="<?= $fitlifeBasePath ?>/views/nutrition/result.php" id="calcForm">
        <?= fitlife_csrf_input() ?>
        <h3>Nutrition Calculator</h3>

        <div class="row">
            <label>Weight (kg) <span class="req">*</span></label>
            <input required name="weight" type="number" step="0.1" min="30" max="300">
        </div>

        <div class="row">
            <label>Height (cm) <span class="req">*</span></label>
            <input required name="height" type="number" step="0.1" min="100" max="250">
        </div>

        <div class="row">
            <label>Age (years) <span class="req">*</span></label>
            <input required name="age" type="number" min="10" max="100">
        </div>

        <div class="row inline">
            <label>Gender <span class="req">*</span></label>
            <div class="inline-options">
                <label><input required type="radio" name="gender" value="male" checked> Male</label>
                <label><input required type="radio" name="gender" value="female"> Female</label>
            </div>
        </div>

        <div class="row">
            <label>Activity level <span class="req">*</span></label>
            <select required name="activity">
                <option value="">Select activity</option>
                <option value="Sedentary">Sedentary (little/no exercise)</option>
                <option value="Light">Light (1–3 days/week)</option>
                <option value="Moderate">Moderate (3–5 days/week)</option>
                <option value="Active">Active (6–7 days/week)</option>
                <option value="Veryactive">Very active (very intense)</option>
            </select>
        </div>

        <div class="row inline">
            <label>Goal <span class="req">*</span></label>
            <div class="inline-options">
                <label><input required type="radio" name="goal" value="bulk"> Bulking</label>
                <label><input required type="radio" name="goal" value="cut"> Cutting</label>
                <label><input required type="radio" name="goal" value="maintain" checked> Maintenance</label>
            </div>
        </div>

        <div class="row">
            <label>Preferred protein (optional)</label>
            <select name="pref_protein">
                <option value="">No preference</option>
                <option value="chicken">Chicken</option>
                <option value="beef">Beef</option>
                <option value="fish">Fish</option>
                <option value="vegan">Vegan</option>
            </select>
        </div>

        <div class="form-actions">
            <button class="btn primary" type="submit">Calculate</button>
            <button type="reset" class="btn ghost">Reset</button>
        </div>

        <p class="note">
            Note: Results are estimates only. For medical conditions or special needs,
            please consult a nutritionist or your doctor.
        </p>
    </form>
</main>
<script>
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
        localStorage.setItem('fitlife_theme', 'pro');
    } else {
        body.classList.remove('theme-pro');
        body.classList.add('theme-light');
        localStorage.setItem('fitlife_theme', 'light');
    }
});
</script>
</body>
</html>

<?php include __DIR__ . '/../templates/footer.php'; ?>
