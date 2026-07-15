<?php
include __DIR__ . '/../templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FitLife | Home</title>
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/styleauth.css">
</head>

<body class="home">

<?php if ($isAuthenticated && !$hasGymAssignment): ?>
<section class="gym-setup-banner" aria-label="Gym owner setup">
    <div>
        <strong>Ready to manage your gym with FitLife?</strong>
        <span>Create your gym account and open your secure management dashboard.</span>
    </div>
    <a href="<?= $fitlifeBasePath ?>/views/gym/setup.php">Set up your gym</a>
</section>
<?php endif; ?>

<section class="hero">
    <div class="hero-text">
        <h1>Welcome to<br>Your Strongest Self!</h1>
        <p>Start today, push your limits, and transform your body and mind.</p>
    
        <?php if ($hasGymAssignment): ?>
            <a href="<?= $fitlifeBasePath ?>/views/dashboard/index.php" class="btn-orange">Open Dashboard</a>
        <?php elseif ($isAuthenticated): ?>
            <a href="<?= $fitlifeBasePath ?>/views/gym/setup.php" class="btn-orange">Set Up Your Gym</a>
        <?php else: ?>
            <a href="<?= $fitlifeBasePath ?>/views/auth/register.php" class="btn-orange">Get Started</a>
        <?php endif; ?>
    </div>

    <div class="hero-img">
        <img src="<?= $fitlifeBasePath ?>/public/images2/fitimg.png" alt="Fit Girl">
    </div>
</section>


<!-- SERVICES-->
<section class="services">
    <div class="service">
        <img src="<?= $fitlifeBasePath ?>/public/images2/dumbbellOrg.png" alt="Plans">
        <h3>Workout Plans</h3>
        <p>Customized workout routines for all fitness levels.</p>
    </div>

    <div class="service">
        <img src="<?= $fitlifeBasePath ?>/public/images2/dietOrg.png" alt="Nutrition">
        <h3>Nutrition</h3>
        <p>Smart nutrition guidance tailored to your body and goals.</p>
    </div>

    <div class="service">
        <img src="<?= $fitlifeBasePath ?>/public/images2/wellbeingOrg.png" alt="Community">
        <h3>Well-being</h3>
        <p>Stay motivated with a supportive fitness community.</p>
    </div>
</section>


<!--  PROGRAMS -->
<section class="programs">
    <h2>Our Programs</h2>

    <div class="program-grid">

        <div class="program-card">
            <img src="<?= $fitlifeBasePath ?>/public/images2/muscle_gain.png" alt="Muscle Gain">
            <h3>Muscle Gain</h3>
            <p>Programs to build lean muscle efficiently.</p>
            <a href="<?= $fitlifeBasePath ?>/views/programs/programs.php">Learn More</a>
        </div>

        <div class="program-card">
            <img src="<?= $fitlifeBasePath ?>/public/images2/fat_loss.png" alt="Fat Loss">
            <h3>Fat Loss</h3>
            <p>Burn fat and keep your strength with smart plans.</p>
            <a href="<?= $fitlifeBasePath ?>/views/programs/programs.php">Learn More</a>
        </div>

        <div class="program-card">
            <img src="<?= $fitlifeBasePath ?>/public/images2/strength.png" alt="Strength">
            <h3>Strength</h3>
            <p>Increase your power and overall performance.</p>
            <a href="<?= $fitlifeBasePath ?>/views/programs/programs.php">Learn More</a>
        </div>

        <div class="program-card">
            <img src="<?= $fitlifeBasePath ?>/public/images2/general_fitness.png" alt="General Fitness">
            <h3>General Fitness</h3>
            <p>Stay active, fit, and healthy.</p>
            <a href="<?= $fitlifeBasePath ?>/views/programs/programs.php">Learn More</a>
        </div>

    </div>
</section>


<!--  GALLERY-->
<section class="gallery">
    <h2>Transformation & Training Moments</h2>
    <p class="gallery-subtext">Real people. Real progress. Real transformation.</p>
    <div class="gallery-divider"></div>
    <div class="gallery-grid">
        <img src="<?= $fitlifeBasePath ?>/public/images2/gallery_1.png" alt="Training hard in the gym">
        <img src="<?= $fitlifeBasePath ?>/public/images2/gallery_2.png" alt="Focused fat loss workout">
        <img src="<?= $fitlifeBasePath ?>/public/images2/gallery_3.png" alt="Strength and conditioning">
    </div>
</section>


<!-- ABOUT -->
<section class="about">
    <h2>About</h2>
    <p>
        FitLife helps you reach your fitness goals with personalized plans,
        nutrition guidance, and a supportive community.
        <br><br>
        <b>This project was created by Ayham Taha and Rawad Choubasy
        for a university project at Beirut Arab University.</b>
    </p>
</section>


<!-- FOOTER -->


</body>
</html>
<?php include __DIR__ . '/../templates/footer.php'; ?>
