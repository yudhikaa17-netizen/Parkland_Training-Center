<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes (Public)
$routes->get('login', 'Auth::index');
$routes->post('login/attempt', 'Auth::attemptLogin');
$routes->get('login/quick/(:segment)', 'Auth::quickLogin/$1');
$routes->match(['get', 'post'], 'logout', 'Auth::logout');
$routes->match(['get', 'post'], 'auth/logout', 'Auth::logout');
$routes->match(['get', 'post'], 'signout', 'Auth::logout');
$routes->match(['get', 'post'], 'sign-out', 'Auth::logout');
$routes->post('auth/switch-role', 'Auth::switchRole');

// Public Attendance Routes (Dapat diakses tanpa login)
$routes->get('attendance', 'Attendance::index');
$routes->get('attendance/kiosk', 'Attendance::kiosk');
$routes->match(['get', 'post'], 'attendance/tap', 'Attendance::tapRfid');

// Default Route -> Redirect to dashboard or login
$routes->get('/', 'Dashboard::index', ['filter' => 'auth']);

// UNIS SQL Server API Integration Endpoints (Direct & Proxy)
$routes->group('api', static function ($routes) {
    // Health Check
    $routes->get('health', 'ApiController::health');
    $routes->get('unis/health', 'ApiController::health');

    // Users List
    $routes->get('users', 'ApiController::users');
    $routes->get('unis/users', 'ApiController::users');

    // User by NIK: /api/user/nik/{NIK}
    $routes->get('user/nik/(:segment)', 'ApiController::userByNik/$1');
    $routes->get('unis/user/nik/(:segment)', 'ApiController::userByNik/$1');
    $routes->get('unis/lookup-nik', 'ApiController::lookupNik');

    // User by Card HEX: /api/user/card/{HEXA}
    $routes->get('user/card/(:segment)', 'ApiController::userByCard/$1');
    $routes->get('unis/user/card/(:segment)', 'ApiController::userByCard/$1');
    $routes->get('unis/lookup-card', 'ApiController::lookupCard');

    // Trainee Auto-Import from UNIS
    $routes->post('unis/import-trainee', 'ApiController::importTrainee');
});

// Protected Routes (Superadmin, Trainer, Guest have access to Dashboard & Info Program)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // 1. Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // 2. Info Program
    $routes->get('info-program', 'InfoProgram::index');

    // 3. Tracking Trainee & Skill Matrix (Read-Only untuk semua pengguna / Guest)
    $routes->get('trainees', 'Trainee::index');
    $routes->get('trainees/detail/(:num)', 'Trainee::detail/$1');

    // Superadmin & Trainer Only (Input data & Mutasi)
    $routes->group('', ['filter' => 'role:superadmin,trainer'], static function ($routes) {
        // Save induction checklist
        $routes->post('trainees/save-induction/(:num)', 'Trainee::saveInduction/$1');

        // 4. Input Data Harian (Mass Entry & Evaluation)
        $routes->get('daily-input', 'DailyInput::index');
        $routes->get('daily-input/find-rfid', 'DailyInput::findByRfid');
        $routes->post('daily-input/motoric', 'DailyInput::saveMotoric');
        $routes->post('daily-input/hardskill', 'DailyInput::saveHardSkill');
        $routes->post('daily-input/ojt', 'DailyInput::saveOjt');

        // 6. Peminjaman Operator (Read)
        $routes->get('loans', 'Loan::index');

        // 7. Materi Training (Read & Mark Completion)
        $routes->get('materials', 'Material::index');
        $routes->post('materials/mark-completion', 'Material::markCompletion');
        $routes->match(['get', 'post'], 'materials/delete-completion/(:num)', 'Material::deleteCompletion/$1');
    });

    // Superadmin Only (Full CRUD Control & Data Reset)
    $routes->group('', ['filter' => 'role:superadmin'], static function ($routes) {
        // Material CRUD mutations (Superadmin Only)
        $routes->post('materials/store', 'Material::store');
        $routes->post('materials/create', 'Material::store');
        $routes->match(['get', 'post'], 'materials/delete/(:num)', 'Material::delete/$1');
        // Trainee CRUD mutations
        $routes->post('trainees/store', 'Trainee::store');
        $routes->post('trainees/create', 'Trainee::store');
        $routes->post('trainees/update/(:num)', 'Trainee::update/$1');
        $routes->post('trainees/update-stage/(:num)', 'Trainee::updateStage/$1');
        $routes->match(['get', 'post'], 'trainees/delete/(:num)', 'Trainee::delete/$1');

        // Attendance CRUD mutations
        $routes->post('attendance/store-manual', 'Attendance::storeManual');
        $routes->post('attendance/manual', 'Attendance::storeManual');
        $routes->match(['get', 'post'], 'attendance/delete/(:num)', 'Attendance::delete/$1');

        // Loan CRUD mutations
        $routes->post('loans/store', 'Loan::store');
        $routes->post('loans/create', 'Loan::store');
        $routes->match(['get', 'post'], 'loans/return/(:num)', 'Loan::returnLoan/$1');
        $routes->match(['get', 'post'], 'loans/delete/(:num)', 'Loan::delete/$1');

        // Content Manager / CMS & Web Settings (Superadmin)
        $routes->get('content-manager', 'ContentManager::index');
        $routes->post('content-manager/save-announcement', 'ContentManager::saveAnnouncement');
        $routes->post('content-manager/store-topic', 'ContentManager::storeTopic');
        $routes->post('content-manager/update-topic/(:num)', 'ContentManager::updateTopic/$1');
        $routes->match(['get', 'post'], 'content-manager/delete-topic/(:num)', 'ContentManager::deleteTopic/$1');
        $routes->post('content-manager/save-thresholds', 'ContentManager::saveThresholds');
        $routes->post('content-manager/save-induction-modules', 'ContentManager::saveInductionModules');
        $routes->post('content-manager/save-factories', 'ContentManager::saveFactories');
        $routes->post('content-manager/save-locations', 'ContentManager::saveLocationStructure');

        // User Management (Superadmin)
        $routes->post('content-manager/store-user', 'ContentManager::storeUser');
        $routes->post('content-manager/update-user/(:num)', 'ContentManager::updateUser/$1');
        $routes->match(['get', 'post'], 'content-manager/delete-user/(:num)', 'ContentManager::deleteUser/$1');

        // Reset Data
        $routes->match(['get', 'post'], 'data/reset-all', 'DataManagement::resetAll');
    });
});
