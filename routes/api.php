<?php

use App\Http\Controllers\API\AgencyController;
use App\Http\Controllers\API\AppSettingController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\AssessmentYearController;
use App\Http\Controllers\API\AWSImageRecognitionController;
use App\Http\Controllers\API\BloodGroupController;
use App\Http\Controllers\API\BusinessCategoryController;
use App\Http\Controllers\API\BusinessLevelController;
use App\Http\Controllers\API\BusinessSubCategoryController;
use App\Http\Controllers\API\BusinessTypeController;
use App\Http\Controllers\API\ClassificationController;
use App\Http\Controllers\API\CommercialVehicleController;
use App\Http\Controllers\API\CooperateController;
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
use App\Http\Controllers\API\PropertyController;
use App\Http\Controllers\API\PropertyTypeController;
use App\Http\Controllers\API\PropertyUseController;
use App\Http\Controllers\API\RegistrationOptionController;
use App\Http\Controllers\API\ResidentialController;
use App\Http\Controllers\API\RevenueItemController;
use App\Http\Controllers\API\RevenueTypeController;
use App\Http\Controllers\API\SettlementTypeController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\SignageController;
use App\Http\Controllers\API\SpouseController;
use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\TicketAgentCategoryController;
use App\Http\Controllers\API\TicketAgentController;
use App\Http\Controllers\API\TicketAgentStatusController;
use App\Http\Controllers\API\TicketAgentTypeController;
use App\Http\Controllers\API\TicketAgentWalletController;
use App\Http\Controllers\API\TicketBulkVendingController;
use App\Http\Controllers\API\TicketCategoryController;
use App\Http\Controllers\API\TicketEnforcementController;
use App\Http\Controllers\API\TicketVendingController;
use App\Http\Controllers\API\TitleController;
use App\Http\Controllers\API\UserConfirmationController as APIUserConfirmationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserFacialBiometricController;
use App\Http\Controllers\API\UserUpdatePasswordController;
use App\Http\Controllers\API\UserVerificationController;
use App\Http\Controllers\API\VehicleEnumerationVerificationController;
use App\Http\Controllers\API\WalletFundTransferController;
use App\Http\Controllers\UserConfirmationController;
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
    Route::post('update-password', UserUpdatePasswordController::class)->middleware('auth:sanctum');
    Route::get('app-settings', AppSettingController::class)->middleware('auth:sanctum');
    Route::get('assessment-years', AssessmentYearController::class);
    Route::get('business-levels', BusinessLevelController::class);
    Route::get('business-types', BusinessTypeController::class);
    Route::get('business-categories', BusinessCategoryController::class);
    Route::get('business-sub-categories/{business_category_id}', BusinessSubCategoryController::class);
    Route::get('classifications', ClassificationController::class);
    Route::get('states', StateController::class);
    Route::get('registration-options', RegistrationOptionController::class);
    Route::post('user-registration-verification', UserVerificationController::class)->middleware('auth:sanctum');
    Route::patch('user-facial-biometric', UserFacialBiometricController::class);
    Route::get('demand-notice-categories', DemandNoticeCategoryController::class);
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
    Route::apiResource('signage', SignageController::class);
    Route::apiResource('assessments', AssessmentController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::get('assessment/{identifier}', [AssessmentController::class, 'indentifier']);
    Route::apiResource('agencies', AgencyController::class)->only(['index']);
    Route::get('revenue-items-agency/{agency_id}', [RevenueItemController::class, 'revenue_item_agency']);
    Route::apiResource('cooperates', CooperateController::class);
    Route::apiResource('commercial-vehicles', CommercialVehicleController::class);
    Route::apiResource('properties', PropertyController::class);
    Route::apiResource('property-types', PropertyTypeController::class)->only(['index']);;
    Route::apiResource('property-uses', PropertyUseController::class)->only(['index']);;
    Route::apiResource('users', UserController::class)->middleware('auth:sanctum')->only(['index']);
    Route::get('user/email-phone-number', [UserController::class, 'email_phone_number'])->middleware('auth:sanctum');
    Route::post('user-verification', [UserController::class, 'user_verification']);
    Route::apiResource('ticket-vending', TicketVendingController::class)->middleware('auth:sanctum')->only(['index', 'store', 'show']);
    Route::apiResource('ticket-bulk-vending', TicketBulkVendingController::class)->middleware('auth:sanctum')->only(['store', 'show', 'index']);
    Route::apiResource('ticket-agents', TicketAgentController::class)->middleware('auth:sanctum')->only(['store', 'show', 'index', 'update']);
    Route::apiResource('ticket-agent-wallet-transactions', TicketAgentWalletController::class)->middleware('auth:sanctum')->only(['show', 'index']);
    Route::apiResource('ticket-enforcements', TicketEnforcementController::class)->middleware('auth:sanctum');
    Route::get('ticket-categories', TicketCategoryController::class);
    Route::post('vehicle-enumeration-verifications', VehicleEnumerationVerificationController::class)->middleware('auth:sanctum');
    Route::get('ticket-agent-categories', TicketAgentCategoryController::class)->middleware('auth:sanctum');
    Route::get('ticket-agent-types', TicketAgentTypeController::class)->middleware('auth:sanctum');
    Route::get('ticket-agent-status', TicketAgentStatusController::class)->middleware('auth:sanctum');
    Route::post('wallet-fund-transfer', WalletFundTransferController::class)->middleware('auth:sanctum');
    Route::get('initiate-liveness', [AWSImageRecognitionController::class, 'initiate_liveness']);
    Route::post('liveness-results', [AWSImageRecognitionController::class, 'liveness_results']);
    Route::post('user-identity-confirmation', [APIUserConfirmationController::class, 'initial_user_identity_confirmation']);
    Route::post('user-identity-token-confirmation', [APIUserConfirmationController::class, 'user_identity_token_confirmation']);
    //Route::apiResource('users', UserController::class);
});
