<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/security.php';

fitlife_require_login();

require_once __DIR__ . '/../../auth/dbconn.php';
require_once __DIR__ . '/../../../includes/authorization.php';

$currentStaff = fitlife_require_gym_staff($conn);
$dashboardFlashes = fitlife_take_flashes();
$fitlifeBasePath = fitlife_escape(FITLIFE_BASE_PATH);
