<?php
// ═══════════════════════════════════════════════════════════════════════
// LEAD MANAGEMENT ROUTES
// Add these inside the Route::middleware('auth')->group(function () { ... });
// block in routes/web.php — same placement style as the Loan Inquiry routes.
//
// Permission strings (view-leads / create-leads / edit-leads / delete-leads)
// are generated automatically by database/seeders/PermissionSeeder.php once
// 'leads' is added to its $modules array — see updated seeder.
// ═══════════════════════════════════════════════════════════════════════

use App\Http\Controllers\LeadController;

// ── Sub-cat 1: Lead Capture (Add New Lead + Import Excel) ───────────────
Route::get('/leads/capture',        [LeadController::class, 'create'])->middleware('permission:create-leads')->name('leads.capture');
Route::post('/leads',               [LeadController::class, 'store'])->middleware('permission:create-leads')->name('leads.store');
Route::post('/leads/import-excel',  [LeadController::class, 'importExcel'])->middleware('permission:create-leads')->name('leads.import-excel');

// ── Brand → Model cascade (shared by Capture, List filters, Assignment filters) ──
Route::get('/categories/{brand}/models', [LeadController::class, 'getModelsForBrand'])->middleware('permission:view-leads')->name('leads.models-for-brand');

// ── Sub-cat 2: Lead List ────────────────────────────────────────────────
Route::get('/leads/list',           [LeadController::class, 'list'])->middleware('permission:view-leads')->name('leads.list');
Route::get('/leads/{lead}',         [LeadController::class, 'show'])->middleware('permission:view-leads')->name('leads.show');
Route::put('/leads/{lead}',         [LeadController::class, 'update'])->middleware('permission:edit-leads')->name('leads.update');
Route::delete('/leads/{lead}',      [LeadController::class, 'destroy'])->middleware('permission:delete-leads')->name('leads.destroy');

// ── Sub-cat 3: Lead Assignment ──────────────────────────────────────────
Route::get('/leads/assignment',               [LeadController::class, 'assignment'])->middleware('permission:view-leads')->name('leads.assignment');
Route::post('/leads/assign',                   [LeadController::class, 'assignLeads'])->middleware('permission:edit-leads')->name('leads.assign');
Route::get('/leads/{lead}/assignment-history', [LeadController::class, 'assignmentHistory'])->middleware('permission:view-leads')->name('leads.assignment-history');

// ── Sub-cat 4: Lead Follow-Up ───────────────────────────────────────────
Route::get('/leads/follow-up',               [LeadController::class, 'followUp'])->middleware('permission:view-leads')->name('leads.follow-up');
Route::get('/leads/{lead}/follow-up-detail', [LeadController::class, 'followUpDetail'])->middleware('permission:view-leads')->name('leads.follow-up-detail');
Route::post('/leads/{lead}/follow-up',       [LeadController::class, 'storeFollowUp'])->middleware('permission:edit-leads')->name('leads.store-followup');
