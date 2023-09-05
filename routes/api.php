<?php

use App\Http\Controllers\API\AppSettingController;
use App\Http\Controllers\API\AssessmentYearController;
use App\Http\Controllers\API\BloodGroupController;
use App\Http\Controllers\API\BusinessCategoryController;
use App\Http\Controllers\API\BusinessLevelController;
use App\Http\Controllers\API\BusinessSubCategoryController;
use App\Http\Controllers\API\BusinessTypeController;
use App\Http\Controllers\API\ClassificationController;
use App\Http\Controllers\API\DemandNoticeCategoryController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\GenoTypeController;
use App\Http\Controllers\API\IncomeRangeController;
use App\Http\Controllers\API\IndividualController;
use App\Http\Controllers\API\LocalGovernmentAreaController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\MaritalStatusController;
use App\Http\Controllers\API\MarketNameController;
use App\Http\Controllers\API\NationalityController;
use App\Http\Controllers\API\OccupationController;
use App\Http\Controllers\API\PropertyCategoryController;
use App\Http\Controllers\API\RegistrationOptionController;
use App\Http\Controllers\API\ResidentialController;
use App\Http\Controllers\API\RevenueTypeController;
use App\Http\Controllers\API\SettlementTypeController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\SpouseController;
use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\TicketAgentCategoryController;
use App\Http\Controllers\API\TicketBulkVendingController;
use App\Http\Controllers\API\TicketCategoryController;
use App\Http\Controllers\API\TicketEnforcementController;
use App\Http\Controllers\API\TicketVendingController;
use App\Http\Controllers\API\TitleController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserFacialBiometricController;
use App\Http\Controllers\API\UserVerificationController;
use App\Http\Controllers\API\WalletFundTransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1')->group(function () {
    Route::post('login', LoginController::class);
    Route::post('logout', LogoutController::class)->middleware('auth:sanctum');
    Route::get('app-settings', AppSettingController::class)->middleware('auth:sanctum');
    Route::get('assessment-years', AssessmentYearController::class);
    Route::get('business-levels', BusinessLevelController::class);
    Route::get('business-types', BusinessTypeController::class);
    Route::get('business-categories', BusinessCategoryController::class);
    Route::get('business-sub-categories/{business_category_id}', BusinessSubCategoryController::class);
    Route::get('classifications', ClassificationController::class);
    Route::get('states', StateController::class);
    Route::get('registration-options', RegistrationOptionController::class);
    Route::post('user-registration-verification', UserVerificationController::class);
    Route::patch('user-facial-biometric', UserFacialBiometricController::class);
    //Route::get('demand-notice-categories', DemandNoticeCategoryController::class);
    Route::get('local-government-areas/{state_id}', LocalGovernmentAreaController::class);
    Route::get('market-names', MarketNameController::class);
    Route::get('nationalities', NationalityController::class);
    Route::get('occupations', OccupationController::class);
    Route::get('property-categories', PropertyCategoryController::class);
    Route::get('revenue-types', RevenueTypeController::class);
    Route::get('settlement-types', SettlementTypeController::class);
    Route::get('blood-groups', BloodGroupController::class);
    Route::get('income-ranges', IncomeRangeController::class);
    Route::get('geno-types', GenoTypeController::class);
    Route::get('marital-status', MaritalStatusController::class);
    Route::get('titles', TitleController::class);
    Route::apiResource('forgot-password', ForgotPasswordController::class)->only(['store', 'update']);
    Route::apiResource('individuals', IndividualController::class);
    Route::apiResource('residential-address', ResidentialController::class);
    Route::apiResource('spouse', SpouseController::class);
    Route::apiResource('shops', ShopController::class);
    Route::apiResource('users', UserController::class)->middleware('auth:sanctum')->only(['index']);
    Route::apiResource('ticket-vending', TicketVendingController::class)->middleware('auth:sanctum')->only(['index', 'store', 'show']);
    Route::apiResource('ticket-bulk-vending', TicketBulkVendingController::class)->middleware('auth:sanctum')->only(['store', 'show', 'index']);
    Route::post('ticket-enforcement', TicketEnforcementController::class)->middleware('auth:sanctum');
    Route::get('ticket-categories', TicketCategoryController::class);
    Route::get('ticket-agent-categories', TicketAgentCategoryController::class)->middleware('auth:sanctum');
    Route::post('wallet-fund-transfer', WalletFundTransferController::class)->middleware('auth:sanctum');
    //Route::apiResource('users', UserController::class);
});
