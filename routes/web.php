<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ClipPaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClipWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::any('/', function () {
    return redirect('/contrato');
});

// Rutas públicas para el formulario de contratos
Route::get('/contrato', [ContratoController::class, 'mostrarFormulario'])->name('contrato.formulario');
Route::get('/contrato/descargar/{token}', [ContratoController::class, 'descargar'])->name('contrato.descargar');

// Rutas para descargar PDFs bajo demanda (se generan en el momento)
Route::get('/pdf/recibo/{token}', [ClipPaymentController::class, 'descargarRecibo'])->name('pdf.recibo');
Route::get('/pdf/contrato/{token}', [ClipPaymentController::class, 'descargarContrato'])->name('pdf.contrato');

// Rutas para Clip Payments (redirecciones del navegador)
Route::get('/clip/pago/{pago}', [ClipPaymentController::class, 'iniciarPago'])->name('clip.iniciar-pago');
Route::get('/clip/success/{token}', [ClipPaymentController::class, 'success'])->name('clip.success');
Route::get('/clip/error/{token}', [ClipPaymentController::class, 'error'])->name('clip.error');
Route::get('/clip/cancel/{token}', [ClipPaymentController::class, 'cancel'])->name('clip.cancel');

// Ruta para consultar estado del pago (opcional, para debugging)
Route::get('/clip/estado/{pago}', [ClipPaymentController::class, 'consultarEstado'])->name('clip.consultar-estado');

// Webhook de Clip (debe ser POST y sin protección CSRF)
Route::post('/webhook/clip', [ClipWebhookController::class, 'handleWebhook'])->name('webhook.clip')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/webhook/clip/test', [ClipWebhookController::class, 'test'])->name('webhook.clip.test');
Route::post('/webhook/clip/test', [ClipWebhookController::class, 'test'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Rutas del Panel de Administración
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminController::class, 'panel'])->name('admin.panel');
Route::get('/admin/contratos', [AdminController::class, 'contratos'])->name('admin.contratos');
Route::get('/admin/pagos', [AdminController::class, 'pagos'])->name('admin.pagos');
Route::get('/admin/contrato/{token}', [AdminController::class, 'verContrato'])->name('admin.ver-contrato');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
