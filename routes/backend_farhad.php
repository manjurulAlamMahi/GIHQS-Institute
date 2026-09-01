<?php

use App\Http\Controllers\Backend\Farhad\AboutContactController;
use App\Http\Controllers\Backend\Farhad\AboutInstituteController;
use App\Http\Controllers\Backend\Farhad\BannerController;
use App\Http\Controllers\Backend\Farhad\VisionMissionValueController;
use App\Http\Controllers\Backend\Farhad\StrategicAdvisoryController;
use App\Http\Controllers\Backend\Farhad\HomeServicesPathwaysController;
use App\Http\Controllers\Backend\Farhad\HomeFlagshipCertificationsController;
use App\Http\Controllers\Backend\Farhad\AdvisoryServicesController;
use App\Http\Controllers\Backend\Farhad\RequestAdvisoryConsultationController;
use App\Http\Controllers\Backend\Farhad\AccreditationReviewController;
use App\Http\Controllers\Backend\Farhad\AccreditationHeaderController;
use App\Http\Controllers\Backend\Farhad\OtherPagesController;
use App\Http\Controllers\Backend\Farhad\AccreditationDetailController;
use App\Http\Controllers\Backend\Farhad\AccreditationFeeController;
use App\Http\Controllers\Backend\Farhad\AccreditationApplyHeroController;
use App\Http\Controllers\Backend\Farhad\PoliciesGovernanceController;
use App\Http\Controllers\Backend\Farhad\ContactMessageController;
use App\Http\Controllers\Backend\Farhad\CertificationApplicationController;
use App\Http\Controllers\Backend\Farhad\AdvisoryRequestController;
use App\Http\Controllers\Backend\Farhad\AccreditationApplicationController;
use App\Http\Controllers\Backend\Farhad\DashboardController;
use App\Http\Controllers\Backend\Farhad\CatalogueController;
use App\Http\Controllers\Backend\Farhad\CatalogueHtmlResourceController;
use App\Http\Controllers\Backend\Farhad\CatalogueCertificationController;
use App\Http\Controllers\Backend\Farhad\CatalogueOtherController;
use App\Http\Controllers\Backend\Farhad\ExamController;
use App\Http\Controllers\Backend\Farhad\MembershipPackageController;
use App\Http\Controllers\Backend\Farhad\PackageController;
use App\Http\Controllers\Backend\Farhad\PathwayResultController;
use App\Http\Controllers\Backend\Farhad\PathwayQuestionController;
use App\Http\Controllers\Backend\Farhad\SliderController;
use App\Http\Controllers\Backend\Farhad\StatusController;
use App\Http\Controllers\Backend\Farhad\MemberController;
use App\Http\Controllers\Backend\Farhad\OrderController;
use App\Http\Controllers\Backend\Setting\AdminSettingController;
use App\Http\Controllers\Backend\Setting\MailSettingController;
use App\Http\Controllers\Backend\Setting\ManagerController;
use App\Http\Controllers\Backend\Setting\ProfileSettingController;
use App\Http\Controllers\Backend\Setting\SocialSettingController;
use App\Http\Controllers\Backend\Setting\ApiSettingController;
use App\Http\Controllers\Backend\Setting\WebsiteSettingController;
use App\Http\Controllers\Backend\Setting\CertificateSettingController;
use App\Http\Controllers\Backend\Farhad\CeActivityController;
use App\Http\Controllers\Backend\Farhad\EmailLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard route
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Banners routes
    Route::get('banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('banners/{banner}', [BannerController::class, 'show'])->name('banners.show');
    Route::get('banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Sliders routes
    Route::get('sliders', [SliderController::class, 'index'])->name('sliders.index');
    Route::get('sliders/create', [SliderController::class, 'create'])->name('sliders.create');
    Route::post('sliders', [SliderController::class, 'store'])->name('sliders.store');
    Route::get('sliders/{slider}', [SliderController::class, 'show'])->name('sliders.show');
    Route::get('sliders/{slider}/edit', [SliderController::class, 'edit'])->name('sliders.edit');
    Route::put('sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update');
    Route::delete('sliders/{slider}', [SliderController::class, 'destroy'])->name('sliders.destroy');


    // Packages routes
    Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

    // Catalogue HTML documents (modules, story guides, toolkits, worksheets)
    Route::get('catalogues/{catalogue}/html-resources', [CatalogueHtmlResourceController::class, 'index'])
        ->name('catalogue-html-resources.index');
    Route::post('catalogues/{catalogue}/html-resources', [CatalogueHtmlResourceController::class, 'store'])
        ->name('catalogue-html-resources.store');
    Route::put('html-resources/{htmlResource}', [CatalogueHtmlResourceController::class, 'update'])
        ->name('catalogue-html-resources.update');
    Route::delete('html-resources/{htmlResource}', [CatalogueHtmlResourceController::class, 'destroy'])
        ->name('catalogue-html-resources.destroy');
    Route::post('html-resource-licenses/{htmlResourceLicense}/revoke', [CatalogueHtmlResourceController::class, 'revokeLicense'])
        ->name('html-resource-licenses.revoke');
    Route::post('html-resource-licenses/{htmlResourceLicense}/restore', [CatalogueHtmlResourceController::class, 'restoreLicense'])
        ->name('html-resource-licenses.restore');

    // Catalogue routes
    Route::resource('catalogues', CatalogueController::class)->except(['show']);
    Route::resource('catalogue-certifications', CatalogueCertificationController::class)->except(['show']);
    Route::resource('catalogue-others', CatalogueOtherController::class)->except(['show']);

    // Exam routes
    Route::resource('exams', ExamController::class)->except(['show']);
    Route::post('exams/{exam}/toggle-status', [ExamController::class, 'toggleStatus'])->name('exams.toggle-status');

    // Membership Packages routes
    Route::get('membership-packages/{id}/clone', [MembershipPackageController::class, 'clone'])->name('membership-packages.clone');
    Route::resource('membership-packages', MembershipPackageController::class)->except(['show']);

    // About Institute routes (edit & update only)
    Route::get('about-institute', [AboutInstituteController::class, 'edit'])->name('about-institute.edit');
    Route::put('about-institute/{id}', [AboutInstituteController::class, 'update'])->name('about-institute.update');

    // About Contact routes (edit & update only)
    Route::get('about-contact', [AboutContactController::class, 'edit'])->name('about-contact.edit');
    Route::put('about-contact/{id}', [AboutContactController::class, 'update'])->name('about-contact.update');

    // Vision Mission Values routes (edit & update only)
    Route::get('vision-mission-values', [VisionMissionValueController::class, 'edit'])->name('vision-mission-values.edit');
    Route::put('vision-mission-values/{id}', [VisionMissionValueController::class, 'update'])->name('vision-mission-values.update');

    // Strategic Advisory routes (edit & update only)
    Route::get('strategic-advisory', [StrategicAdvisoryController::class, 'edit'])->name('strategic-advisory.edit');
    Route::put('strategic-advisory/{id}', [StrategicAdvisoryController::class, 'update'])->name('strategic-advisory.update');

    // Home Page -> Services & Pathways routes (edit & update only)
    Route::get('home-services-pathways', [HomeServicesPathwaysController::class, 'edit'])->name('home-services-pathways.edit');
    Route::put('home-services-pathways/{id}', [HomeServicesPathwaysController::class, 'update'])->name('home-services-pathways.update');

    // Home Page -> Flagship Certifications routes (edit & update only)
    Route::get('home-flagship-certifications', [HomeFlagshipCertificationsController::class, 'edit'])->name('home-flagship-certifications.edit');
    Route::put('home-flagship-certifications/{id}', [HomeFlagshipCertificationsController::class, 'update'])->name('home-flagship-certifications.update');

    // Advisory Services routes (edit & update only)
    Route::get('advisory-services', [AdvisoryServicesController::class, 'edit'])->name('advisory-services.edit');
    Route::put('advisory-services/{id}', [AdvisoryServicesController::class, 'update'])->name('advisory-services.update');

    // Request Advisory Consultation routes (edit & update only)
    Route::get('request-advisory-consultation', [RequestAdvisoryConsultationController::class, 'edit'])->name('request-advisory-consultation.edit');
    Route::put('request-advisory-consultation/{id}', [RequestAdvisoryConsultationController::class, 'update'])->name('request-advisory-consultation.update');

    // Accreditation Review routes (edit & update only)
    Route::get('accreditation-review', [AccreditationReviewController::class, 'edit'])->name('accreditation-review.edit');
    Route::put('accreditation-review/{id}', [AccreditationReviewController::class, 'update'])->name('accreditation-review.update');

    // Other Pages routes
    Route::get('other-pages', [OtherPagesController::class, 'edit'])->name('other-pages.edit');
    Route::put('other-pages', [OtherPagesController::class, 'update'])->name('other-pages.update');

    // Accreditation Header routes (edit & update only)
    Route::get('accreditation-header', [AccreditationHeaderController::class, 'edit'])->name('accreditation-header.edit');
    Route::put('accreditation-header/{id}', [AccreditationHeaderController::class, 'update'])->name('accreditation-header.update');

    // Accreditation Details routes (edit & update only)
    Route::get('accreditation-details', [AccreditationDetailController::class, 'edit'])->name('accreditation-details.edit');
    Route::put('accreditation-details/{id}', [AccreditationDetailController::class, 'update'])->name('accreditation-details.update');

    // Accreditation Fees routes (edit & update only)
    Route::get('accreditation-fees', [AccreditationFeeController::class, 'edit'])->name('accreditation-fees.edit');
    Route::put('accreditation-fees/{id}', [AccreditationFeeController::class, 'update'])->name('accreditation-fees.update');

    // Apply Accreditation Hero routes (edit & update only)
    Route::get('accreditation-apply-hero', [AccreditationApplyHeroController::class, 'edit'])->name('accreditation-apply-hero.edit');
    Route::put('accreditation-apply-hero/{id}', [AccreditationApplyHeroController::class, 'update'])->name('accreditation-apply-hero.update');

    // Policies & Governance routes (edit & update only)
    Route::get('policies-governance', [PoliciesGovernanceController::class, 'edit'])->name('policies-governance.edit');
    Route::put('policies-governance/{id}', [PoliciesGovernanceController::class, 'update'])->name('policies-governance.update');

    // Contact Messages routes
    Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{contact_message}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::get('contact-messages/{contact_message}/edit', [ContactMessageController::class, 'edit'])->name('contact-messages.edit');
    Route::patch('contact-messages/{contact_message}/status', [ContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');
    Route::delete('contact-messages/{contact_message}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::post('contact-messages/{contact_message}/reply', [ContactMessageController::class, 'reply'])->name('contact-messages.reply');

    // Certification Applications routes
    Route::get('certification-applications', [CertificationApplicationController::class, 'index'])->name('certification-applications.index');
    Route::get('certification-applications/{certification_application}', [CertificationApplicationController::class, 'show'])->name('certification-applications.show');
    Route::patch('certification-applications/{certification_application}/status', [CertificationApplicationController::class, 'updateStatus'])->name('certification-applications.update-status');
    Route::delete('certification-applications/{certification_application}', [CertificationApplicationController::class, 'destroy'])->name('certification-applications.destroy');
    Route::post('user-exam-overrides', [CertificationApplicationController::class, 'storeOrUpdateOverride'])->name('user-exam-overrides.storeOrUpdate');
    Route::get('exam-overrides', [CertificationApplicationController::class, 'overridesIndex'])->name('exam-overrides.index');
    Route::get('exam-overrides/{certification_application}', [CertificationApplicationController::class, 'overridesShow'])->name('exam-overrides.show');

    // CE Activities routes
    Route::get('ce-activities', [CeActivityController::class, 'index'])->name('ce-activities.index');
    Route::get('ce-activities/{ce_activity}', [CeActivityController::class, 'show'])->name('ce-activities.show');
    Route::patch('ce-activities/{ce_activity}/status', [CeActivityController::class, 'updateStatus'])->name('ce-activities.update-status');
    Route::delete('ce-activities/{ce_activity}', [CeActivityController::class, 'destroy'])->name('ce-activities.destroy');

    // Advisory Requests routes
    Route::get('advisory-requests', [AdvisoryRequestController::class, 'index'])->name('advisory-requests.index');
    Route::get('advisory-requests/{advisory_request}', [AdvisoryRequestController::class, 'show'])->name('advisory-requests.show');
    Route::post('advisory-requests/{advisory_request}/generate-payment-link', [AdvisoryRequestController::class, 'generatePaymentLink'])->name('advisory-requests.generate-payment-link');
    Route::patch('advisory-requests/{advisory_request}/status', [AdvisoryRequestController::class, 'updateStatus'])->name('advisory-requests.update-status');
    Route::delete('advisory-requests/{advisory_request}', [AdvisoryRequestController::class, 'destroy'])->name('advisory-requests.destroy');

    // Accreditation Applications routes
    Route::get('accreditation-applications', [AccreditationApplicationController::class, 'index'])->name('accreditation-applications.index');
    Route::get('accreditation-applications/{accreditation_application}', [AccreditationApplicationController::class, 'show'])->name('accreditation-applications.show');
    Route::post('accreditation-applications/{accreditation_application}/generate-payment-link', [AccreditationApplicationController::class, 'generatePaymentLink'])->name('accreditation-applications.generate-payment-link');
    Route::patch('accreditation-applications/{accreditation_application}/status', [AccreditationApplicationController::class, 'updateStatus'])->name('accreditation-applications.update-status');
    Route::post('accreditation-applications/{accreditation_application}/regenerate-certificate', [AccreditationApplicationController::class, 'regenerateCertificate'])->name('accreditation-applications.regenerate-certificate');
    Route::delete('accreditation-applications/{accreditation_application}', [AccreditationApplicationController::class, 'destroy'])->name('accreditation-applications.destroy');

    // Email Logs routes
    Route::get('email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');



    // Members routes
    Route::get('members', [MemberController::class, 'index'])->name('members.index');
    Route::post('members/{id}/update-membership', [MemberController::class, 'updateMembership'])->name('members.update-membership');
    Route::post('members/{id}/cancel-subscription', [MemberController::class, 'cancelSubscription'])->name('members.cancel-subscription');

    // Orders routes
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/refund-requests', [OrderController::class, 'refundRequests'])->name('orders.refund-requests');
    Route::patch('orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{id}/refund', [OrderController::class, 'refund'])->name('orders.refund');
    Route::get('orders/{id}/refund-history', [OrderController::class, 'refundHistory'])->name('orders.refund-history');
    Route::post('orders/{id}/reject-refund', [OrderController::class, 'rejectRefund'])->name('orders.reject-refund');

    // Pathway Results routes
    Route::resource('pathway-results', PathwayResultController::class);

    // Pathway Questions routes
    Route::resource('pathway-questions', PathwayQuestionController::class);

    //Status
    Route::post('/update-status', [StatusController::class, 'update'])->name('status.update');

    // ------------------- Settings routes start ------------------
    // Profile settings routes
    Route::get('settings/profile', [ProfileSettingController::class, 'edit'])->name('profile-settings.edit');
    Route::post('settings/profile/change-password', [ProfileSettingController::class, 'changePassword'])->name('profile-settings.change-password');
    Route::post('settings/profile/{id}', [ProfileSettingController::class, 'update'])->name('profile-settings.update');

    // Manager management routes
    Route::get('settings/managers', [ManagerController::class, 'index'])->name('managers.index');
    Route::post('settings/managers', [ManagerController::class, 'store'])->name('managers.store');
    Route::put('settings/managers/{id}', [ManagerController::class, 'update'])->name('managers.update');
    Route::delete('settings/managers/{id}', [ManagerController::class, 'destroy'])->name('managers.destroy');

    // Social settings routes
    Route::get('settings/social', [SocialSettingController::class, 'edit'])->name('social-settings.edit');
    Route::post('settings/social', [SocialSettingController::class, 'update'])->name('social-settings.update');

    // Mail settings routes
    Route::get('settings/mail', [MailSettingController::class, 'edit'])->name('mail-settings.edit');
    Route::post('settings/mail', [MailSettingController::class, 'update'])->name('mail-settings.update');

    // Api Settings routes
    Route::get('settings/api', [ApiSettingController::class, 'edit'])->name('api-settings.edit');
    Route::post('settings/api', [ApiSettingController::class, 'update'])->name('api-settings.update');

    // Website Settings routes
    Route::get('settings/website', [WebsiteSettingController::class, 'edit'])->name('website-settings.edit');
    Route::post('settings/website', [WebsiteSettingController::class, 'update'])->name('website-settings.update');



    // Admin Settings routes
    Route::get('settings/admin', [AdminSettingController::class, 'edit'])->name('admin-settings.edit');
    Route::post('settings/admin', [AdminSettingController::class, 'update'])->name('admin-settings.update');

    // Certificate Settings routes
    Route::get('settings/certificate', [CertificateSettingController::class, 'edit'])->name('certificate-settings.edit');
    Route::post('settings/certificate', [CertificateSettingController::class, 'update'])->name('certificate-settings.update');
    // ------------------- Settings routes end ------------------
});
