<?php

require_once __DIR__ . '/../app/models/Barber.php';

if (file_exists(__DIR__ . '/../app/models/Service.php')) {
    require_once __DIR__ . '/../app/models/Service.php';
}

if (file_exists(__DIR__ . '/../app/models/Client.php')) {
    require_once __DIR__ . '/../app/models/Client.php';
}

if (file_exists(__DIR__ . '/../app/models/Scheduling.php')) {
    require_once __DIR__ . '/../app/models/Scheduling.php';
}