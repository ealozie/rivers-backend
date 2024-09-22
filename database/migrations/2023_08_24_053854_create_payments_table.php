<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            /*
            ServiceUrl
ServiceUsername
ServicePassword
FTPUrl
FTPUsername
FTPPassword
Payments
Payment
IsRepeated
ProductGroupCode
PaymentLogId
CustReference
AlternateCustReference
Amount
PaymentStatus
PaymentMethod
PaymentReference
TerminalId
ChannelName
Location
IsReversal
PaymentDate
SettlementDate
InstitutionId
InstitutionName
BranchName
BankName
FeeName
CustomerName
OtherCustomerInfo
ReceiptNo
CollectionsAccount
ThirdPartyCode
Payments
Payment
ItemName
ItemCode
ItemAmount
LeadBankCode
LeadBankCbnCode
LeadBankName
CategoryCode
CategoryName
ItemQuantity
BankCode
CustomerAddress
CustomerPhoneNumber
DepositorName
DepositorSlipNumber
PaymentCurrency
OriginalPaymentLogId
OriginalPaymentReference
Teller
            */
            $table->string('service_url')->nullable();
            $table->string('service_username')->nullable();
            $table->string('service_password')->nullable();
            $table->string('ftp_url')->nullable();
            $table->string('ftp_username')->nullable();
            $table->string('ftp_password')->nullable();
            $table->string('is_repeated')->nullable();
            $table->string('product_group_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_log_id')->nullable();
            $table->string('customer_reference')->nullable();
            $table->string('alternate_customer_reference')->nullable();
            $table->double('amount', 12, 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('terminal_id')->nullable();
            $table->string('channel_name')->nullable();
            $table->string('location')->nullable();
            $table->string('is_reversal')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('settlement_date')->nullable();
            $table->string('institution_id')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('fee_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('other_customer_info')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('collections_account')->nullable();
            $table->string('third_party_code')->nullable();
            $table->json('payment_items')->nullable();
            $table->string('item_name', 50)->nullable();
            $table->string('item_code', 20)->nullable();
            $table->double('item_amount', 12, 2)->nullable();
            $table->string('lead_bank_code', 20)->nullable();
            $table->string('lead_bank_cbn_code', 20)->nullable();
            $table->string('lead_bank_name')->nullable();
            $table->string('category_code', 20)->nullable();
            $table->string('category_name')->nullable();
            $table->string('item_quantity')->nullable();
            $table->string('bank_code', 20)->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_phone_number')->nullable();
            $table->string('depositor_name')->nullable();
            $table->string('depositor_slip_number')->nullable();
            $table->string('payment_currency')->nullable();
            $table->string('original_payment_log_id')->nullable();
            $table->string('original_payment_reference')->nullable();
            $table->string('teller')->nullable();
            $table->boolean('is_credited')->default(false);
            // $table->string('uuid')->nullable();
            // $table->string('transaction_id')->nullable();
            // $table->string('reference_number')->nullable();
            // $table->string('transaction_date')->nullable();
            // $table->foreignId('user_id')->nullable();
            // $table->string('payer_name')->nullable();
            // $table->string('merchant_reference')->nullable();
            // $table->string('retrieval_reference_number')->nullable();
            // $table->string('merchant_customer_id')->nullable();
            // $table->string('payer_phone_number')->nullable();
            // $table->string('payer_address')->nullable();
            // $table->string('receipt_number')->nullable();
            // $table->string('payment_gateway');
            // $table->string('amount')->nullable();
            // $table->string('transaction_status')->nullable();
            // $table->string('transaction_response')->nullable();
            // $table->string('method')->nullable();
            // $table->string('payment_channel')->nullable();
            // $table->string('payment_channel_id')->nullable();
            // $table->string('deposit_slip_number')->nullable();
            // $table->string('bank_name', 50)->nullable();
            // $table->string('bank_code', 20)->nullable();
            // $table->string('product_name')->nullable();
            // $table->string('payment_id')->nullable();
            // $table->string('product_id')->nullable();
            // $table->string('branch_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
