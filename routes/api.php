<?php

use App\Http\Controllers\API\AWSImageRecognitionController;
use App\Http\Controllers\API\AccountManagerController;
use App\Http\Controllers\API\AgencyController;
use App\Http\Controllers\API\AppSettingController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\AssessmentYearController;
use App\Http\Controllers\API\AuditTrailController;
use App\Http\Controllers\API\BloodGroupController;
use App\Http\Controllers\API\BusinessCategoryController;
use App\Http\Controllers\API\BusinessLevelController;
use App\Http\Controllers\API\BusinessSubCategoryController;
use App\Http\Controllers\API\BusinessTypeController;
use App\Http\Controllers\API\ClassificationController;
use App\Http\Controllers\API\CommercialVehicleController;
use App\Http\Controllers\API\CooperateController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DemandNoticeCategoryController;
use App\Http\Controllers\API\DemandNoticeCategoryItemController;
use App\Http\Controllers\API\DemandNoticeController;
use App\Http\Controllers\API\DemandNoticeItemController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\DocumentLifeSpanController;
use App\Http\Controllers\API\DocumentTollGateEntryController;
use App\Http\Controllers\API\DocumentTypeController;
use App\Http\Controllers\API\DocumentTypeTollGateController;
use App\Http\Controllers\API\EntitySearchController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\GenoTypeController;
use App\Http\Controllers\API\IncomeRangeController;
use App\Http\Controllers\API\IndividualController;
use App\Http\Controllers\API\IndividualRelativeController;
use App\Http\Controllers\API\LocalGovernmentAreaController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\LogoutTokenValidationController;
use App\Http\Controllers\API\MaritalStatusController;
use App\Http\Controllers\API\MarketNameController;
use App\Http\Controllers\API\MastController;
use App\Http\Controllers\API\MonifyWebhookController;
use App\Http\Controllers\API\NationalityController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\OccupationController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\PropertyCategoryController;
use App\Http\Controllers\API\PropertyController;
use App\Http\Controllers\API\PropertyTypeController;
use App\Http\Controllers\API\PropertyUseController;
use App\Http\Controllers\API\RegistrationOptionController;
use App\Http\Controllers\API\ResidentialController;
use App\Http\Controllers\API\RevenueItemController;
use App\Http\Controllers\API\RevenueTypeController;
use App\Http\Controllers\API\SMSNotificationController;
use App\Http\Controllers\API\ServiceCategoryController;
use App\Http\Controllers\API\ServiceHistoryController;
use App\Http\Controllers\API\ServiceProviderController;
use App\Http\Controllers\API\ServiceRequestController;
use App\Http\Controllers\API\ServiceSubCategoryController;
use App\Http\Controllers\API\SettlementTypeController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\SignageController;
use App\Http\Controllers\API\SpouseController;
use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\StreetController;
use App\Http\Controllers\API\SuperAgentController;
use App\Http\Controllers\API\TicketAgentCategoryController;
use App\Http\Controllers\API\TicketAgentController;
use App\Http\Controllers\API\TicketAgentStatusController;
use App\Http\Controllers\API\TicketAgentTypeController;
use App\Http\Controllers\API\TicketAgentWalletController;
use App\Http\Controllers\API\TicketBulkVendingController;
use App\Http\Controllers\API\TicketCategoryController;
use App\Http\Controllers\API\TicketEnforcementComplianceController;
use App\Http\Controllers\API\TicketEnforcementController;
use App\Http\Controllers\API\TicketVendingController;
use App\Http\Controllers\API\TitleController;
use App\Http\Controllers\API\TollGateCategoryController;
use App\Http\Controllers\API\UserConfirmationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserFacialBiometricController;
use App\Http\Controllers\API\UserUpdatePasswordController;
use App\Http\Controllers\API\UserVerificationController;
use App\Http\Controllers\API\VehicleCategoryController;
use App\Http\Controllers\API\VehicleEnumerationVerificationController;
use App\Http\Controllers\API\VehicleManufacturerController;
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

Route::prefix("v1")->group(function () {
    Route::get("token-verification", LogoutTokenValidationController::class);
    Route::post("login", LoginController::class);
    Route::post("logout", LogoutController::class)->middleware("auth:sanctum");
    Route::post(
        "update-password",
        UserUpdatePasswordController::class
    )->middleware("auth:sanctum");
    Route::apiResource("app-settings", AppSettingController::class)
        ->middleware("auth:sanctum")
        ->only(["index", "update"]);
    Route::get("public-app-settings", [
        AppSettingController::class,
        "public_app_settings",
    ]);
    Route::get("assessment-years", AssessmentYearController::class);
    Route::get("business-levels", BusinessLevelController::class);
    Route::get("business-types", BusinessTypeController::class);
    Route::get("business-categories", BusinessCategoryController::class);
    Route::get(
        "business-sub-categories/{business_category_id}",
        BusinessSubCategoryController::class
    );
    Route::get("classifications", ClassificationController::class);
    Route::get("states", StateController::class);
    Route::get("registration-options", RegistrationOptionController::class);
    Route::post("user-registration-verification", [
        UserVerificationController::class,
        "initial_registration_request",
    ]);
    Route::post("user-phone-number-verification", [
        UserVerificationController::class,
        "user_phone_number_confirmation",
    ]);
    Route::get("phone-number-confirmation/{phone_number}/{account_type?}", [
        UserVerificationController::class,
        "phone_number_confirmation_code",
    ]);
    Route::post("phone-number-confirmation", [
        UserVerificationController::class,
        "phone_number_confirmation_store",
    ]);
    Route::patch("user-facial-biometric", UserFacialBiometricController::class);
    Route::apiResource(
        "demand-notice-categories",
        DemandNoticeCategoryController::class
    );
    Route::apiResource(
        "demand-notice-categories-item",
        DemandNoticeCategoryItemController::class
    )->middleware("auth:sanctum");
    Route::get(
        "local-government-areas/{state_id}",
        LocalGovernmentAreaController::class
    );
    Route::get("market-names", MarketNameController::class);
    Route::get("nationalities", NationalityController::class);
    Route::get("occupations", OccupationController::class);
    Route::get("property-categories", PropertyCategoryController::class);
    Route::get("revenue-types", RevenueTypeController::class);
    Route::get("settlement-types", SettlementTypeController::class);
    Route::get("blood-groups", BloodGroupController::class);
    Route::get("income-ranges", IncomeRangeController::class);
    Route::get("geno-types", GenoTypeController::class);
    Route::get("marital-status", MaritalStatusController::class);
    Route::get("titles", TitleController::class);
    Route::apiResource(
        "forgot-password",
        ForgotPasswordController::class
    )->only(["store", "update"]);
    Route::apiResource("individuals", IndividualController::class);
    Route::get("individuals-gender", [IndividualController::class, "gender"]);

    Route::get("individuals-search", [IndividualController::class, "search"]);
    Route::get("individuals-properties/{property_id}", [
        IndividualController::class,
        "get_by_property_id",
    ]);
    Route::post("individuals-birthday/{individual_id}", [
        IndividualController::class,
        "send_birthday_message",
    ]);
    Route::get("individuals-entity-id/{entity_id}", [
        IndividualController::class,
        "show_entity_id",
    ]);
    Route::get("cooperate-entity-id/{entity_id}", [
        CooperateController::class,
        "show_entity_id",
    ]);
    Route::get("cooperate-properties/{property_id}", [
        CooperateController::class,
        "get_by_property_id",
    ]);
    //Route::apiResource('residential-address', ResidentialController::class);
    Route::get("entity-search", EntitySearchController::class);
    Route::apiResource("spouse", SpouseController::class);
    Route::apiResource("shops", ShopController::class)->middleware(
        "auth:sanctum"
    );
    Route::apiResource("masts", MastController::class); //->middleware('auth:sanctum');
    Route::apiResource("streets", StreetController::class);
    Route::apiResource(
        "individual-relatives",
        IndividualRelativeController::class
    )->middleware("auth:sanctum");
    Route::get("individual-relatives-search/{individual_id}", [
        IndividualRelativeController::class,
        "get_relatives",
    ])->middleware("auth:sanctum");
    Route::post("sms-notification-entity-type-id", [
        SMSNotificationController::class,
        "send_sms_via_entity_type_and_id",
    ])->middleware("auth:sanctum");
    Route::get("individual-relatives-verification", [
        IndividualRelativeController::class,
        "verify_relative",
    ])->middleware("auth:sanctum");
    Route::apiResource(
        "service-providers",
        ServiceProviderController::class
    )->middleware("auth:sanctum");
    Route::apiResource(
        "service-requests",
        ServiceRequestController::class
    )->middleware("auth:sanctum");
    Route::apiResource("service-categories", ServiceCategoryController::class);
    Route::apiResource(
        "service-sub-categories",
        ServiceSubCategoryController::class
    );
    Route::apiResource("service-histories", ServiceHistoryController::class);
    Route::get("service-histories-by-service-request/{request_id}", [
        ServiceHistoryController::class,
        "service_history_by_request",
    ]);
    Route::get("shops-search", [ShopController::class, "search"])->middleware(
        "auth:sanctum"
    );
    Route::get("shops-properties/{property_id}", [
        ShopController::class,
        "get_by_property_id",
    ]);
    Route::apiResource(
        "toll-gate-categories",
        TollGateCategoryController::class
    )
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource(
        "document-type-toll-gates",
        DocumentTypeTollGateController::class
    )
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource(
        "document-toll-gates-entries",
        DocumentTollGateEntryController::class
    )
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource("demand-notice-items", DemandNoticeItemController::class)
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource("demand-notices", DemandNoticeController::class)
        ->middleware("auth:sanctum")
        ->only(["index", "store", "update", "show"]);
    Route::get(
        "demand-notices-by-demand-notice-number/{demand_notice_number}",
        [DemandNoticeController::class, "show_by_demand_notice_number"]
    )->middleware("auth:sanctum");
    Route::apiResource("document-types", DocumentTypeController::class)
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource("documents", DocumentController::class)
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource("document-life-spans", DocumentLifeSpanController::class)
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::get("shops-by-user-id/{user_id_or_unique_id}", [
        ShopController::class,
        "show_by_user_id",
    ])->middleware("auth:sanctum");
    Route::patch("shop-link-account/{shop_id}", [
        ShopController::class,
        "link_account",
    ])->middleware("auth:sanctum");
    Route::patch("property-link-account/{property_id}", [
        PropertyController::class,
        "link_account",
    ])->middleware("auth:sanctum");
    Route::patch("mast-link-account/{mast_id}", [
        MastController::class,
        "link_account",
    ])->middleware("auth:sanctum");
    Route::patch("signage-link-account/{signage_id}", [
        SignageController::class,
        "link_account",
    ])->middleware("auth:sanctum");
    Route::apiResource("signage", SignageController::class);
    Route::get("signage-properties/{property_id}", [
        SignageController::class,
        "get_by_property_id",
    ]);
    Route::get("signage-by-user-id/{user_id_or_unique_id}", [
        SignageController::class,
        "show_by_user_id",
    ])->middleware("auth:sanctum");
    Route::get("audit-trails", AuditTrailController::class)->middleware(
        "auth:sanctum"
    );
    Route::apiResource("assessments", AssessmentController::class)->middleware(
        "auth:sanctum"
    );
    Route::get("assessments-statistics", [
        AssessmentController::class,
        "assessments_statistics",
    ])->middleware("auth:sanctum");
    Route::apiResource("notes", NoteController::class)->middleware(
        "auth:sanctum"
    );
    Route::get("entity-notes/{entity_type}/{entity_id}", [
        NoteController::class,
        "notes",
    ])->middleware("auth:sanctum");
    Route::get("entity-account-managers/{entity_type}/{entity_id}", [
        AccountManagerController::class,
        "account_manager_by_entity_id",
    ])->middleware("auth:sanctum");
    Route::get("account-manager-entities/{user_id}", [
        AccountManagerController::class,
        "account_manager_entities",
    ])->middleware("auth:sanctum");
    Route::apiResource(
        "account-managers",
        AccountManagerController::class
    )->middleware("auth:sanctum");
    Route::get("assessments-search", [
        AssessmentController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::get("assessments-agency/{agency_id}", [
        AssessmentController::class,
        "assessments_by_agency_id",
    ])->middleware("auth:sanctum");
    Route::get("assessments-agency-user/{agency_id}/{user_id}", [
        AssessmentController::class,
        "assessments_by_agency_user",
    ])->middleware("auth:sanctum");
    Route::get("assessments-by-user-id/{user_id_or_unique_id}", [
        AssessmentController::class,
        "show_by_user_id",
    ])->middleware("auth:sanctum");
    Route::get("assessments-by-reference-number/{reference_number}", [
        AssessmentController::class,
        "show_by_reference_number",
    ]);
    Route::get("assessments-by-entity-id/{entity_id}", [
        AssessmentController::class,
        "assessment_by_entity_id",
    ])->middleware("auth:sanctum");
    Route::get("assessments-payment-verification/{entity_id}", [
        AssessmentController::class,
        "assessment_payment_verification",
    ])->middleware("auth:sanctum");
    Route::get("assessments-by-phone-number/{phone_number}", [
        AssessmentController::class,
        "show_by_phone_number",
    ])->middleware("auth:sanctum");
    Route::post("assessment-entity-validation", [
        AssessmentController::class,
        "validate_assessment_entity_id",
    ]);
    Route::post("bulk-assessments-with-id", [
        AssessmentController::class,
        "bulk_assessment_store",
    ])->middleware("auth:sanctum");
    Route::post("bulk-assessments-without-id", [
        AssessmentController::class,
        "bulk_assessment_without_id_store",
    ])->middleware("auth:sanctum");
    Route::get("assessment/{identifier}", [
        AssessmentController::class,
        "indentifier",
    ]);
    Route::apiResource("agencies", AgencyController::class)->only([
        "index",
        "show",
        "update",
        "store",
    ]);
    Route::apiResource("revenue-items", RevenueItemController::class)
        ->only(["index", "show", "update", "store"])
        ->middleware("auth:sanctum");
    Route::get("revenue-items-agency/{agency_id}", [
        RevenueItemController::class,
        "revenue_item_agency",
    ]);
    Route::apiResource("cooperates", CooperateController::class);
    Route::get("cooperates-search", [
        CooperateController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::apiResource(
        "commercial-vehicles",
        CommercialVehicleController::class
    );
    Route::get("commercial-vehicles-search", [
        CommercialVehicleController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::get("commercial-vehicles-by-user-id/{user_id_or_unique_id}", [
        CommercialVehicleController::class,
        "show_by_user_id",
    ])->middleware("auth:sanctum");
    Route::apiResource("properties", PropertyController::class); //->middleware('auth:sanctum');
    Route::get("properties-search", [
        PropertyController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::get("properties-by-user-id/{user_id_or_unique_id}", [
        PropertyController::class,
        "show_by_user_id",
    ])->middleware("auth:sanctum");
    Route::apiResource("property-types", PropertyTypeController::class)->only([
        "index",
    ]);
    Route::apiResource("payments", PaymentController::class)
        ->middleware("auth:sanctum")
        ->only(["index", "store"]);
    Route::get("payments-search", [
        PaymentController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::post("payments-webhook", [
        PaymentController::class,
        "payment_webhoook_for_wallet",
    ]);
    Route::post("interswitch/notify", [
        PaymentController::class,
        "interswitch_payment_notification_data_validation",
    ]);
    Route::post("payments-isw-generate-reference", [
        PaymentController::class,
        "payment_generate_reference",
    ]);
    Route::get("payments-isw-reference-verification", [
        PaymentController::class,
        "payment_reference_verification",
    ])->middleware("auth:sanctum");
    Route::get("payments-by-user-id/{user_id_or_unique_id}", [
        PaymentController::class,
        "show_by_user_id",
    ])->middleware("auth:sanctum");
    Route::get("payments-by-reference-number/{reference_number}", [
        PaymentController::class,
        "show_by_reference_number",
    ]);
    Route::apiResource("property-uses", PropertyUseController::class)->only([
        "index",
    ]);
    Route::apiResource("users", UserController::class)->middleware(
        "auth:sanctum"
    );
    Route::get("account-officers", [
        UserController::class,
        "account_officers",
    ])->middleware("auth:sanctum");
    Route::post("assign-permissions/{user_id}", [
        UserController::class,
        "assign_permission",
    ])->middleware("auth:sanctum");
    Route::post("revoke-permissions/{user_id}", [
        UserController::class,
        "revoke_permission",
    ])->middleware("auth:sanctum");
    Route::post("users-advanced-search", [
        UserController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::get("user/email-phone-number", [
        UserController::class,
        "email_phone_number",
    ])->middleware("auth:sanctum");
    Route::post("user-verification", [
        UserController::class,
        "user_verification",
    ]);
    Route::apiResource("ticket-vending", TicketVendingController::class)
        ->middleware("auth:sanctum")
        ->only(["index", "store", "show"]);
    Route::apiResource("permissions", PermissionController::class)
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::get("roles", [
        PermissionController::class,
        "role_index",
    ])->middleware("auth:sanctum");
    Route::post("permissions/roles", [
        PermissionController::class,
        "store_permission_to_roles",
    ])->middleware("auth:sanctum");
    Route::get("roles/permissions/{role}", [
        PermissionController::class,
        "role_permissions_index",
    ])->middleware("auth:sanctum");

    Route::patch("permissions/roles/revoke", [
        PermissionController::class,
        "revoke_permission_from_role",
    ])->middleware("auth:sanctum");

    Route::get("ticket-vending-search", [
        TicketVendingController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-statistics", [
        TicketVendingController::class,
        "ticket_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-statistics-daily", [
        TicketVendingController::class,
        "daily_ticket_vending_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-statistics-weekly", [
        TicketVendingController::class,
        "weekly_ticket_vending_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-statistics-monthly", [
        TicketVendingController::class,
        "monthly_ticket_vending_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-today-collection", [
        TicketVendingController::class,
        "today_collection",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-weekly-collection", [
        TicketVendingController::class,
        "weekly_collection",
    ])->middleware("auth:sanctum");
    Route::get("ticket-vending-monthly-collection", [
        TicketVendingController::class,
        "monthly_collection",
    ])->middleware("auth:sanctum");
    Route::get("ticket-total-vending-statistics-daily", [
        TicketVendingController::class,
        "daily_ticket_total_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-total-vending-statistics-weekly", [
        TicketVendingController::class,
        "weekly_ticket_total_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-total-vending-statistics-monthly", [
        TicketVendingController::class,
        "monthly_ticket_total_statistics",
    ])->middleware("auth:sanctum");
    /*Make Total ticket for today, this week, month and last month*/
    Route::get("ticket-vending-by-agent-id/{ticket_agent_id}", [
        TicketVendingController::class,
        "tickets_by_agent",
    ])->middleware("auth:sanctum");
    Route::get("ticket-total-vending-statistics", [
        TicketVendingController::class,
        "ticket_total_statistics",
    ])->middleware("auth:sanctum");
    Route::get("ticket-bulk-vending-by-agent-id/{ticket_agent_id}", [
        TicketBulkVendingController::class,
        "tickets_by_agent",
    ])->middleware("auth:sanctum");
    Route::apiResource(
        "ticket-bulk-vending",
        TicketBulkVendingController::class
    )
        ->middleware("auth:sanctum")
        ->only(["store", "show", "index"]);
    Route::get("ticket-bulk-vending-search", [
        TicketBulkVendingController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::apiResource(
        "vehicle-manufacturers",
        VehicleManufacturerController::class
    )
        ->middleware("auth:sanctum")
        ->only(["index", "show"]);
    Route::apiResource("vehicle-categories", VehicleCategoryController::class)
        ->middleware("auth:sanctum")
        ->only(["index"]);
    Route::apiResource("ticket-agents", TicketAgentController::class)
        ->middleware("auth:sanctum")
        ->only(["store", "show", "index", "update"]);
    Route::apiResource("super-agents", SuperAgentController::class)->middleware(
        "auth:sanctum"
    );
    Route::patch("restore-assign-super-agent/{id}", [
        SuperAgentController::class,
        "restore_super_agent",
    ])->middleware("auth:sanctum");
    Route::patch("change-agent-super-agent/{agent_id}", [
        TicketAgentController::class,
        "change_agent_super_agent",
    ])->middleware("auth:sanctum");
    Route::patch("remove-agent-from-super-agent/{agent_id}", [
        TicketAgentController::class,
        "remove_agent_super_agent",
    ])->middleware("auth:sanctum");
    Route::get("ticket-agents-wallet-transactions/{agent_id}", [
        TicketAgentController::class,
        "ticket_agent_transactions",
    ])->middleware("auth:sanctum");
    Route::get("ticket-agent-sales", [
        TicketAgentController::class,
        "get_agents_with_hightest_sales",
    ])->middleware("auth:sanctum");
    Route::get("ticket-sales-per-local-government", [
        TicketVendingController::class,
        "sales_by_local_government",
    ])->middleware("auth:sanctum");
    Route::get(
        "ticket-sales-statistics-by-local-government-ticket-categories",
        [
            TicketVendingController::class,
            "sales_statistics_by_local_government_and_ticket_categories",
        ]
    )->middleware("auth:sanctum");
    Route::get("ticket-sales-by-local-government-ticket-categories", [
        TicketVendingController::class,
        "sales_by_local_government_and_ticket_categories",
    ])->middleware("auth:sanctum");
    Route::get("ticket-sales-statistics-by-zone-ticket-categories", [
        TicketVendingController::class,
        "sales_statistics_by_zone_and_ticket_categories",
    ])->middleware("auth:sanctum");
    Route::get("ticket-sales-by-zone-ticket-categories", [
        TicketVendingController::class,
        "sales_by_zone_and_ticket_categories",
    ])->middleware("auth:sanctum");
    Route::post("plate-number-recent-records", [
        TicketVendingController::class,
        "plate_number_recent_records",
    ]);
    Route::apiResource(
        "ticket-agent-wallet-transactions",
        TicketAgentWalletController::class
    )
        ->middleware("auth:sanctum")
        ->only(["show", "index"]);
    Route::apiResource(
        "ticket-enforcements",
        TicketEnforcementController::class
    )->middleware("auth:sanctum");
    Route::get("ticket-enforcements-search", [
        TicketEnforcementController::class,
        "search",
    ])->middleware("auth:sanctum");
    Route::get("ticket-agent-enforcements/{agent_id}", [
        TicketEnforcementController::class,
        "ticket_agent_enforcements",
    ])->middleware("auth:sanctum");
    Route::apiResource("ticket-categories", TicketCategoryController::class);
    Route::get(
        "ticket-enforcement-compliance",
        TicketEnforcementComplianceController::class
    );
    Route::post(
        "vehicle-enumeration-verifications",
        VehicleEnumerationVerificationController::class
    )->middleware("auth:sanctum");
    Route::get(
        "ticket-agent-categories",
        TicketAgentCategoryController::class
    )->middleware("auth:sanctum");
    Route::get(
        "ticket-agent-types",
        TicketAgentTypeController::class
    )->middleware("auth:sanctum");
    Route::get(
        "ticket-agent-status",
        TicketAgentStatusController::class
    )->middleware("auth:sanctum");
    Route::post(
        "wallet-fund-transfer",
        WalletFundTransferController::class
    )->middleware("auth:sanctum");
    Route::get("initiate-liveness", [
        AWSImageRecognitionController::class,
        "initiate_liveness",
    ])->middleware("auth:sanctum");
    Route::post("liveness-results", [
        AWSImageRecognitionController::class,
        "liveness_results",
    ])->middleware("auth:sanctum");
    Route::post("user-identity-confirmation", [
        UserConfirmationController::class,
        "initial_user_identity_confirmation",
    ]);
    Route::post("user-identity-token-confirmation", [
        UserConfirmationController::class,
        "user_identity_token_confirmation",
    ]);
    /*Monify webhook begins*/
    Route::post("/monify/transaction-completion", [
        MonifyWebhookController::class,
        "transaction_completion",
    ]);
    Route::post("/monify/refund-completion", [
        MonifyWebhookController::class,
        "refund_completion",
    ]);
    Route::post("/monify/disbursement", [
        MonifyWebhookController::class,
        "disbursement",
    ]);
    Route::post("/monify/transaction-completed", [
        MonifyWebhookController::class,
        "settlement",
    ]);
    Route::get("/dashboard-aggregate-shops", [
        DashboardController::class,
        "shops_aggregates",
    ]);
    Route::get("/dashboard-aggregate-properties", [
        DashboardController::class,
        "properties_aggregates",
    ]);
    Route::get("/dashboard-aggregate-signage", [
        DashboardController::class,
        "signage_aggregates",
    ]);
    Route::get("/dashboard-aggregate-individuals", [
        DashboardController::class,
        "individuals_aggregates",
    ]);
    Route::get("/dashboard-aggregate-cooperates", [
        DashboardController::class,
        "cooperates_aggregates",
    ]);
    Route::get("/dashboard-aggregate-vehicles", [
        DashboardController::class,
        "vehicles_aggregates",
    ]);
    //Route::apiResource('users', UserController::class);
});
