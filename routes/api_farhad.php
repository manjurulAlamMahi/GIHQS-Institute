<?php

use App\Http\Controllers\Api\AboutContactApiController;
use App\Http\Controllers\Api\AboutInstituteApiController;
use App\Http\Controllers\Api\AdvisoryRequestApiController;
use App\Http\Controllers\Api\AccreditationApplicationApiController;
use App\Http\Controllers\Api\AccreditationApplyHeroApiController;
use App\Http\Controllers\Api\AccreditationDetailApiController;
use App\Http\Controllers\Api\AccreditationFeeApiController;
use App\Http\Controllers\Api\AccreditationHeaderApiController;
use App\Http\Controllers\Api\AccreditationReviewApiController;
use App\Http\Controllers\Api\AdvisoryServicesApiController;
use App\Http\Controllers\Api\HomeFlagshipCertificationsApiController;
use App\Http\Controllers\Api\HomeServicesPathwaysApiController;
use App\Http\Controllers\Api\PoliciesGovernanceApiController;
use App\Http\Controllers\Api\RequestAdvisoryConsultationApiController;
use App\Http\Controllers\Api\StrategicAdvisoryApiController;
use App\Http\Controllers\Api\VisionMissionValueApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CertificationApplicationController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\PathwayApiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\MembershipPackageApiController;
use App\Http\Controllers\Api\CatalogueApiController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ClassmarkerWebhookController;
use App\Http\Controllers\Api\CeActivityApiController;
use App\Http\Controllers\Api\WebsiteSettingApiController;
use App\Http\Controllers\Api\OtherPagesApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['setLang'])->group(function () {
    // lang specific
});

// Authentication routes
// 'throttle:auth' keeps credential stuffing and OTP guessing to a crawl; the api
// group's general limiter is far too generous for these endpoints.
Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/verify-otp', [AuthController::class, 'registerVerifyOtp']);
    Route::post('/login', [AuthController::class, 'login']);

    // Forgot Password for customer
    Route::post('/password/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
});

Route::post('/logout', [AuthController::class, 'logout']);



// get Certification catalogues
Route::get('/certification-catalogues', [CertificationApplicationController::class, 'certificationCatalogues']);




//Profile Settings both Customer and Driver/Deliveryman
Route::middleware('auth:api')->group(function () {

    // About Contact Us Message route
    Route::post('/about-contact-message', [ContactMessageController::class, 'store']);

    // Apply Accreditation route
    Route::post('/apply-accreditation', [AccreditationApplicationApiController::class, 'store']);
    Route::get('/apply-accreditation', [AccreditationApplicationApiController::class, 'index']);
    Route::get('/apply-accreditation/{id}', [AccreditationApplicationApiController::class, 'show']);

    // Apply for Certification routes
    Route::get('/get-apply-for-certification', [CertificationApplicationController::class, 'index']);
    Route::get('/get-apply-for-certification/{id}', [CertificationApplicationController::class, 'show']);
    Route::get('/apply-for-certification', [CertificationApplicationController::class, 'index']);
    Route::get('/apply-for-certification/{id}', [CertificationApplicationController::class, 'show']);
    Route::post('/apply-for-certification', [CertificationApplicationController::class, 'store']);

    // Apply for Advisory Request route
    Route::get('/get-advisory-request', [AdvisoryRequestApiController::class, 'index']);
    Route::get('/get-advisory-request/{id}', [AdvisoryRequestApiController::class, 'show']);
    Route::post('/advisory-request', [AdvisoryRequestApiController::class, 'store']);


    // language toggle update
    Route::post('/language-toggle', [ProfileController::class, 'toggleLanguage']);

    // Profile routes
    Route::get('/profile-info', [ProfileController::class, 'profileInfo']);
    Route::post('/profile-update', [ProfileController::class, 'profileUpdate']);
    Route::post('/profile-change-password', [ProfileController::class, 'changePassword']);
    Route::post('/profile-change-address', [ProfileController::class, 'changeAddress']);
    Route::post('/profile-delete', [ProfileController::class, 'profileDelete']);
    Route::post('/profile-update-location', [ProfileController::class, 'updateLocation']); // update location lat long
    Route::get('/profile/dashboard-stats', [ProfileController::class, 'dashboardStats']);
    Route::get('/profile/dashboard-overview', [ProfileController::class, 'dashboardOverview']);

    // Stripe Checkout
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::post('/membership/checkout', [CheckoutController::class, 'membershipCheckout']);
    Route::get('/profile/subscription', [CheckoutController::class, 'getSubscriptionDetails']);
    Route::post('/profile/subscription/cancel', [CheckoutController::class, 'cancelSubscription']);
    Route::get('/profile/orders', [CheckoutController::class, 'orderHistory']);
    Route::get('/profile/orders/{orderId}/invoice', [CheckoutController::class, 'downloadInvoice'])->name('membership.checkout.invoice');
    Route::post('/profile/orders/{id}/request-refund', [CheckoutController::class, 'requestRefund']);
    Route::get('/profile/purchased-catalogues', [CatalogueApiController::class, 'purchasedCatalogues']);
    Route::get('/profile/purchased-catalogues/{id}', [CatalogueApiController::class, 'purchasedCatalogueShow']);
    Route::get('/profile/exams/{examId}/attempts', [CatalogueApiController::class, 'examAttempts']);
    Route::get('/profile/exams/{catalogueExamId}', [CatalogueApiController::class, 'getLocalExamDetails']);
    Route::post('/profile/exams/{catalogueExamId}/submit', [CatalogueApiController::class, 'submitLocalExam']);
    Route::post('/profile/purchased-catalogues/videos/complete', [CatalogueApiController::class, 'updateVideoProgress']);

    // CE Activities
    Route::get('/profile/ce-activities/tracking', [CeActivityApiController::class, 'trackCredits']);
    Route::get('/profile/ce-activities', [CeActivityApiController::class, 'index']);
    Route::post('/profile/ce-activities', [CeActivityApiController::class, 'store']);
});

// Checkout Callbacks (Public) - Placed before wildcard route to avoid route conflicts
// The cancel callbacks take a record id straight from the query string and flip
// that record to "cancelled". Stripe redirects the browser here with no session
// of ours attached, so they are protected with a signed URL instead: only a link
// this application generated for this purchase is accepted.
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->middleware('signed')->name('checkout.cancel');

// Advisory Checkout Callbacks
Route::get('/advisory/checkout/success', [CheckoutController::class, 'advisorySuccess'])->name('advisory.checkout.success');
Route::get('/advisory/checkout/cancel', [CheckoutController::class, 'advisoryCancel'])->middleware('signed')->name('advisory.checkout.cancel');

// Accreditation Checkout Callbacks
Route::get('/accreditation/checkout/success', [CheckoutController::class, 'accreditationSuccess'])->name('accreditation.checkout.success');
Route::get('/accreditation/checkout/cancel', [CheckoutController::class, 'accreditationCancel'])->middleware('signed')->name('accreditation.checkout.cancel');

// GET checkout supporting both token (api) and session/cookie (web)
Route::middleware('auth:api,web')->get('/checkout/{id}', [CheckoutController::class, 'checkoutGet'])->where('id', '[0-9]+');

// Need for app publications
Route::post('app-account-delete', [ProfileController::class, 'appAccountDelete']); //apps delete account inside public folder html: account-delete.html

// Store FCM Token - FM
Route::middleware('auth:api')->group(function () {
    Route::post('/store-fcm-token', [AuthController::class, 'storeFcmToken']);
    Route::post('/delete-fcm-token', [AuthController::class, 'deleteFcmToken']);
});

//Continue with google and facebook login
// Route::post('/social/login', [SocialLoginController::class, 'SocialLogin']);

// Stripe Webhook
Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook']);

// public routes
Route::get('/pathways/start', [PathwayApiController::class, 'start']);
Route::get('/pathways/step/{option_id}', [PathwayApiController::class, 'getNextStep']);
Route::get('/about-institute', [AboutInstituteApiController::class, 'getAboutInstitute']);
Route::get('/about-contact', [AboutContactApiController::class, 'getAboutContact']);
Route::get('/website-setting', [WebsiteSettingApiController::class, 'getWebsiteSetting']);
Route::get('/other-pages', [OtherPagesApiController::class, 'index']);
Route::get('/other-pages/{slug}', [OtherPagesApiController::class, 'getOtherPage']);
Route::get('/vision-mission-values', [VisionMissionValueApiController::class, 'getVisionMissionValues']);
Route::get('/policies-governance', [PoliciesGovernanceApiController::class, 'getPoliciesGovernance']);
Route::get('/accreditation-review', [AccreditationReviewApiController::class, 'getAccreditationReview']);
Route::get('/strategic-advisory', [StrategicAdvisoryApiController::class, 'getStrategicAdvisory']);
Route::get('/home-services-pathways', [HomeServicesPathwaysApiController::class, 'getHomeServicesPathways']);
Route::get('/home-flagship-certifications', [HomeFlagshipCertificationsApiController::class, 'getHomeFlagshipCertifications']);
Route::get('/accreditation-header', [AccreditationHeaderApiController::class, 'getAccreditationHeader']);
Route::get('/accreditation-details', [AccreditationDetailApiController::class, 'getAccreditationDetails']);
Route::get('/accreditation-fees', [AccreditationFeeApiController::class, 'getAccreditationFees']);
Route::get('/accreditation-apply-hero', [AccreditationApplyHeroApiController::class, 'getAccreditationApplyHero']);
Route::get('/advisory-services', [AdvisoryServicesApiController::class, 'getAdvisoryServices']);
Route::get('/request-advisory-consultation', [RequestAdvisoryConsultationApiController::class, 'getRequestAdvisoryConsultation']);

// Membership Packages
Route::get('/membership-packages', [MembershipPackageApiController::class, 'index']);
Route::get('/membership-packages/{id}', [MembershipPackageApiController::class, 'show']);

// Catalogues & Certificates
Route::get('/catalogues/menu', [CatalogueApiController::class, 'menu']);
Route::get('/catalogues/menu-without-certification', [CatalogueApiController::class, 'menuWithoutCertification']);
Route::get('/catalogues/menu/{id}', [CatalogueApiController::class, 'menuShow']);
Route::get('/catalogues', [CatalogueApiController::class, 'index']);
Route::get('/catalogues/{id}', [CatalogueApiController::class, 'show']);
Route::get('/catalogues/{id}/details', [CatalogueApiController::class, 'details']);
// Public verification lookups disclose holder details, so they get a tighter
// throttle than the rest of the API to make bulk harvesting impractical.
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/certificates/verify/{certificate_id}', [CatalogueApiController::class, 'verifyCertificate']);

    // Accreditation Public Verification
    Route::get('/accreditation/verify/{code}', [AccreditationApplicationApiController::class, 'publicVerify']);
});



// Membership Checkout Callbacks (Public)
Route::get('/membership/checkout/success', [CheckoutController::class, 'membershipSuccess'])->name('membership.checkout.success');
Route::get('/membership/checkout/cancel', [CheckoutController::class, 'membershipCancel'])->middleware('signed')->name('membership.checkout.cancel');

// ClassMarker Webhook (Public)
Route::post('/classmarker/webhook', [ClassmarkerWebhookController::class, 'handleWebhook']);

// SECURITY: Temporary cPanel utility routes removed (/run-migration, /clear-config, /clear-route, /clear-logs).
// These commands should be run through deploy pipeline or CLI access only.


