<?php
// Resolve bootstrap path whether index.php is inside /public or directly at the webroot.
$baseDir = is_dir(__DIR__ . '/app') ? __DIR__ : dirname(__DIR__);
require $baseDir . '/app/bootstrap.php';

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\FormController;
use App\Controllers\SubmissionController;
use App\Controllers\CRMController;
use App\Controllers\ApiUploadController;
use App\Controllers\UserController;
use App\Controllers\SectionController;
use App\Controllers\FormListController;
use App\Controllers\PlatformController;
use App\Controllers\ContentTypeController;
use App\Controllers\ExportImportController;
use App\Controllers\DashboardController;
use App\Controllers\AdminPanelController;
use App\Services\Auth;

$router = new Router();
$auth = new AuthController();
$form = new FormController();
$submission = new SubmissionController();
$crm = new CRMController();
$upload = new ApiUploadController();
$users = new UserController();
$sectionsAdmin = new SectionController();
$formList = new FormListController();
$platformsAdmin = new PlatformController();
$contentTypesAdmin = new ContentTypeController();
$exportImport = new ExportImportController();
$dashboard = new DashboardController();
$adminPanel = new AdminPanelController();

$router->get('/', function () {
    if (Auth::user()) {
        $role = Auth::user()['role'];
        if ($role === 'requester') {
            header('Location: /forms/escolher');
        } else {
            header('Location: /crm');
        }
    } else {
        header('Location: /login');
    }
});

$router->get('/login', [$auth, 'showLogin']);
$router->post('/login', [$auth, 'login']);
$router->get('/register', [$auth, 'registerForm']);
$router->post('/register', [$auth, 'registerSubmit']);
$router->get('/logout', [$auth, 'logout']);

$router->get('/admin/forms', [$form, 'index']);
$router->get('/admin/forms/create', [$form, 'builder']);
$router->get('/admin/forms/{id}/builder', [$form, 'builder']);
$router->post('/admin/forms/save', [$form, 'save']);

$router->get('/admin/users', [$users, 'index']);
$router->post('/admin/users/save', [$users, 'save']);
$router->post('/admin/users/delete', [$users, 'delete']);
$router->get('/admin/sections', [$sectionsAdmin, 'index']);
$router->post('/admin/sections/save', [$sectionsAdmin, 'save']);
$router->get('/admin/platforms', [$platformsAdmin, 'index']);
$router->post('/admin/platforms/save', [$platformsAdmin, 'save']);
$router->get('/admin/content-types', [$contentTypesAdmin, 'index']);
$router->post('/admin/content-types/save', [$contentTypesAdmin, 'save']);
$router->get('/admin/export-import', [$exportImport, 'show']);
$router->get('/admin/export', [$exportImport, 'exportCsv']);
$router->post('/admin/import/preview', [$exportImport, 'importPreview']);
$router->post('/admin/import/save', [$exportImport, 'importSave']);
$router->get('/admin/painel', [$adminPanel, 'index']);
$router->post('/admin/painel/section', [$adminPanel, 'addSection']);
$router->post('/admin/painel/platform', [$adminPanel, 'addPlatform']);
$router->post('/admin/painel/content-type', [$adminPanel, 'addContentType']);

$router->get('/f/{slug}', [$submission, 'publicForm']);
$router->post('/f/{slug}/submit', [$submission, 'submit']);

$router->get('/crm', [$crm, 'kanban']);
$router->post('/crm/status', [$crm, 'updateStatus']);
$router->get('/crm/card/{id}', [$crm, 'card']);
$router->post('/crm/card/{id}/comment', [$crm, 'addComment']);
$router->post('/crm/card/{id}/legend', [$crm, 'saveLegend']);
$router->post('/crm/card/{id}/meta', [$crm, 'saveMeta']);
$router->post('/crm/card/{id}/assign', [$crm, 'assign']);
$router->post('/crm/create', [$crm, 'createManual']);
$router->post('/crm/card/{id}/final-upload', [$crm, 'uploadFinal']);
$router->post('/crm/card/{id}/download', [$crm, 'downloadZip']);

$router->get('/admin/dashboard', [$dashboard, 'index']);
$router->get('/dashboard', function () {
    header('Location: /admin/dashboard');
});

$router->post('/api/upload/init', [$upload, 'init']);
$router->post('/api/upload/chunk', [$upload, 'chunk']);
$router->post('/api/upload/complete', [$upload, 'complete']);

$router->get('/forms/escolher', [$formList, 'choose']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
