 <!-- ========== App Menu ========== -->
 <div class="app-menu navbar-menu">
     <!-- LOGO -->
     <div class="navbar-brand-box">
         <!-- Dark Logo-->
         <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
             <span class="logo-sm">
                 @if (!empty($adminSetting->mini_logo))
                     <img src="{{ asset($adminSetting->mini_logo) }}" alt="Logo" height="22">
                 @endif
             </span>
             <span class="logo-lg">
                 @if (!empty($adminSetting->logo))
                     <img src="{{ asset($adminSetting->logo) }}" alt="Logo" height="35">
                 @endif
             </span>
         </a>
         <!-- Light Logo-->
         <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
             <span class="logo-sm">
                 @if (!empty($adminSetting->mini_logo))
                     <img src="{{ asset($adminSetting->mini_logo) }}" alt="Logo" height="22">
                 @endif
             </span>
             <span class="logo-lg">
                 @if (!empty($adminSetting->logo))
                     <img src="{{ asset($adminSetting->logo) }}" alt="Logo" height="35">
                 @endif
             </span>
         </a>
         <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
             <i class="ri-record-circle-line"></i>
         </button>
     </div>

     <!-- sidebar-user -->
     <div class="dropdown sidebar-user m-1 rounded">
         <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <span class="d-flex align-items-center gap-2">
                 <img class="rounded header-profile-user" src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('backend/assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                 <span class="text-start">
                     <span class="d-block fw-medium sidebar-user-name-text">{{ auth()->user()->full_name }}</span>
                     <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Online</span></span>
                 </span>
             </span>
         </button>
         <div class="dropdown-menu dropdown-menu-end">
             <!-- item-->
             <h6 class="dropdown-header">Welcome {{ auth()->user()->full_name }}!</h6>
             <a class="dropdown-item" href="{{ route('admin.profile-settings.edit') }}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                     class="align-middle">Profile</span></a>
             <!-- Logout -->
             <form method="POST" action="{{ route('logout') }}">
                 @csrf
                 <button type="submit" class="dropdown-item">
                     <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                     <span class="align-middle" data-key="t-logout">Logout</span>
                 </button>
             </form>
         </div>
     </div>

     <!-- sidebar -->
     <div id="scrollbar">
         <div class="container-fluid">

             <div id="two-column-menu">
             </div>
             <ul class="navbar-nav" id="navbar-nav">

                 <!--  Menu -->
                 <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                 <!-- Dashboard -->
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                         <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                     </a>
                 </li>

                 {{-- Content Management Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.about-institute.*') || request()->routeIs('admin.about-contact.*') || request()->routeIs('admin.vision-mission-values.*') || request()->routeIs('admin.strategic-advisory.*') || request()->routeIs('admin.accreditation-review.*') || request()->routeIs('admin.policies-governance.*') || request()->routeIs('admin.other-pages.*') ? '' : 'collapsed' }}"
                         href="#sidebarContentManagement" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.about-institute.*') || request()->routeIs('admin.about-contact.*') || request()->routeIs('admin.vision-mission-values.*') || request()->routeIs('admin.strategic-advisory.*') || request()->routeIs('admin.accreditation-review.*') || request()->routeIs('admin.policies-governance.*') || request()->routeIs('admin.other-pages.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarContentManagement">
                         <i class="ri-pages-line"></i> <span>Content Management</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.about-institute.*') || request()->routeIs('admin.about-contact.*') || request()->routeIs('admin.vision-mission-values.*') || request()->routeIs('admin.strategic-advisory.*') || request()->routeIs('admin.accreditation-review.*') || request()->routeIs('admin.policies-governance.*') || request()->routeIs('admin.other-pages.*') ? 'show' : '' }}"
                         id="sidebarContentManagement">
                         <ul class="nav nav-sm flex-column">
                             {{-- <li class="nav-item">
                                 <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                                     Banners
                                 </a>
                             </li> --}}
                             {{--
                            <li class="nav-item">
                                <a href="{{ route('admin.sliders.index') }}" class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                                    Sliders
                                </a>
                            </li>
                            --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.about-institute.edit') }}" class="nav-link {{ request()->routeIs('admin.about-institute.*') ? 'active' : '' }}">
                                     About Institute
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.vision-mission-values.edit') }}" class="nav-link {{ request()->routeIs('admin.vision-mission-values.*') ? 'active' : '' }}">
                                     Mission, Vision & Values
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.policies-governance.edit') }}" class="nav-link {{ request()->routeIs('admin.policies-governance.*') ? 'active' : '' }}">
                                     Policies & Governance
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.strategic-advisory.edit') }}" class="nav-link {{ request()->routeIs('admin.strategic-advisory.*') ? 'active' : '' }}">
                                     Strategic Advisory
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.accreditation-review.edit') }}" class="nav-link {{ request()->routeIs('admin.accreditation-review.*') ? 'active' : '' }}">
                                     Accreditation Review
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.about-contact.edit') }}" class="nav-link {{ request()->routeIs('admin.about-contact.*') ? 'active' : '' }}">
                                     About Contact
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.other-pages.edit') }}" class="nav-link {{ request()->routeIs('admin.other-pages.*') ? 'active' : '' }}">
                                     Other Pages
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Home Page Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.home-services-pathways.*') || request()->routeIs('admin.home-flagship-certifications.*') ? '' : 'collapsed' }}"
                         href="#sidebarHomePage" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.home-services-pathways.*') || request()->routeIs('admin.home-flagship-certifications.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarHomePage">
                         <i class="ri-home-gear-line"></i> <span>Home Page</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.home-services-pathways.*') || request()->routeIs('admin.home-flagship-certifications.*') ? 'show' : '' }}"
                         id="sidebarHomePage">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.home-services-pathways.edit') }}" class="nav-link {{ request()->routeIs('admin.home-services-pathways.*') ? 'active' : '' }}">
                                     Services & Pathways
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.home-flagship-certifications.edit') }}" class="nav-link {{ request()->routeIs('admin.home-flagship-certifications.*') ? 'active' : '' }}">
                                     Flagship Certifications
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Accreditation Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.accreditation-header.*') || request()->routeIs('admin.accreditation-details.*') || request()->routeIs('admin.accreditation-fees.*') || request()->routeIs('admin.accreditation-apply-hero.*') ? '' : 'collapsed' }}"
                         href="#sidebarAccreditation" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.accreditation-header.*') || request()->routeIs('admin.accreditation-details.*') || request()->routeIs('admin.accreditation-fees.*') || request()->routeIs('admin.accreditation-apply-hero.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarAccreditation">
                         <i class="ri-award-line"></i> <span>Accreditation</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.accreditation-header.*') || request()->routeIs('admin.accreditation-details.*') || request()->routeIs('admin.accreditation-fees.*') || request()->routeIs('admin.accreditation-apply-hero.*') ? 'show' : '' }}"
                         id="sidebarAccreditation">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.accreditation-header.edit') }}" class="nav-link {{ request()->routeIs('admin.accreditation-header.*') ? 'active' : '' }}">
                                     Accreditation Header
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.accreditation-details.edit') }}" class="nav-link {{ request()->routeIs('admin.accreditation-details.*') ? 'active' : '' }}">
                                     Eligibility, Process, Domain, Insights
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.accreditation-fees.edit') }}" class="nav-link {{ request()->routeIs('admin.accreditation-fees.*') ? 'active' : '' }}">
                                     Accreditation Fees
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.accreditation-apply-hero.edit') }}" class="nav-link {{ request()->routeIs('admin.accreditation-apply-hero.*') ? 'active' : '' }}">
                                     Apply Accreditation Hero
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Advisory Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.advisory-services.*') || request()->routeIs('admin.request-advisory-consultation.*') ? '' : 'collapsed' }}"
                         href="#sidebarAdvisory" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.advisory-services.*') || request()->routeIs('admin.request-advisory-consultation.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarAdvisory">
                         <i class="ri-user-star-line"></i> <span>Advisory</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.advisory-services.*') || request()->routeIs('admin.request-advisory-consultation.*') ? 'show' : '' }}"
                         id="sidebarAdvisory">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.advisory-services.edit') }}" class="nav-link {{ request()->routeIs('admin.advisory-services.*') ? 'active' : '' }}">
                                     Advisory Services
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.request-advisory-consultation.edit') }}"
                                     class="nav-link {{ request()->routeIs('admin.request-advisory-consultation.*') ? 'active' : '' }}">
                                     Request Advisory Consultation
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                {{-- Pathway Wizard Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.pathway-*') ? '' : 'collapsed' }}" href="#sidebarPathway" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.pathway-*') ? 'true' : 'false' }}" aria-controls="sidebarPathway">
                         <i class="ri-question-answer-line"></i> <span>Pathway Wizard</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.pathway-*') ? 'show' : '' }}" id="sidebarPathway">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.pathway-questions.index') }}" class="nav-link {{ request()->routeIs('admin.pathway-questions.*') ? 'active' : '' }}">
                                     Questions & Flow
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.pathway-results.index') }}" class="nav-link {{ request()->routeIs('admin.pathway-results.*') ? 'active' : '' }}">
                                     Pathway Results
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li> --}}

                 {{-- nested drop down menu
                 <li class="nav-item">
                     <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                         <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Authentication</span>
                     </a>
                     <div class="collapse menu-dropdown" id="sidebarAuth">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="#sidebarSignIn" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin"> Sign
                                     In
                                 </a>
                                 <div class="collapse menu-dropdown" id="sidebarSignIn">
                                     <ul class="nav nav-sm flex-column">
                                         <li class="nav-item">
                                             <a href="auth-signin-basic.html" class="nav-link" data-key="t-basic"> Basic
                                             </a>
                                         </li>
                                         <li class="nav-item">
                                             <a href="auth-signin-cover.html" class="nav-link" data-key="t-cover"> Cover
                                             </a>
                                         </li>
                                     </ul>
                 --}}

                 {{-- Package Menu
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.packages.*') ? '' : 'collapsed' }}" href="#sidebarPackage" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.packages.*') ? 'true' : 'false' }}" aria-controls="sidebarPackage">
                         <i class="ri-gift-line"></i> <span>Packages</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.packages.*') ? 'show' : '' }}" id="sidebarPackage">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.packages.create') }}" class="nav-link {{ request()->routeIs('admin.packages.create') ? 'active' : '' }}">
                                     Add Package
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.index') ? 'active' : '' }}">
                                     All Packages
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>
                 --}}

                 <!-- Catalogue Menu -->
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.catalogues.*') ? '' : 'collapsed' }}" href="#sidebarCatalogue" data-bs-toggle="collapse"
                         role="button" aria-expanded="{{ request()->routeIs('admin.catalogues.*') ? 'true' : 'false' }}" aria-controls="sidebarCatalogue">
                         <i class="ri-book-open-line"></i> <span>Catalogue</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.catalogues.*') ? 'show' : '' }}" id="sidebarCatalogue">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.catalogues.create') }}" class="nav-link {{ request()->routeIs('admin.catalogues.create') ? 'active' : '' }}">
                                     Add Catalogue Item
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.catalogues.index') }}" class="nav-link {{ request()->routeIs('admin.catalogues.index') ? 'active' : '' }}">
                                     All Catalogue Items
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Exam Questions Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ (request()->routeIs('admin.exams.*') || request()->routeIs('admin.catalogue-certifications.*') || request()->routeIs('admin.exam-overrides.*') || request()->routeIs('admin.catalogue-others.*') || request()->routeIs('admin.certificate-settings.*')) ? '' : 'collapsed' }}" href="#sidebarExam" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ (request()->routeIs('admin.exams.*') || request()->routeIs('admin.catalogue-certifications.*') || request()->routeIs('admin.exam-overrides.*') || request()->routeIs('admin.catalogue-others.*') || request()->routeIs('admin.certificate-settings.*')) ? 'true' : 'false' }}" aria-controls="sidebarExam">
                         <i class="ri-file-list-3-line"></i> <span>Exam Questions</span>
                     </a>
                     <div class="collapse menu-dropdown {{ (request()->routeIs('admin.exams.*') || request()->routeIs('admin.catalogue-certifications.*') || request()->routeIs('admin.exam-overrides.*') || request()->routeIs('admin.catalogue-others.*') || request()->routeIs('admin.certificate-settings.*')) ? 'show' : '' }}" id="sidebarExam">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.exams.create') }}" class="nav-link {{ request()->routeIs('admin.exams.create') ? 'active' : '' }}">
                                     Add Exam
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.exams.index') }}" class="nav-link {{ request()->routeIs('admin.exams.index') ? 'active' : '' }}">
                                     All Exams
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.catalogue-others.index') }}" class="nav-link {{ (request()->routeIs('admin.catalogue-others.index') || request()->routeIs('admin.catalogue-others.edit')) ? 'active' : '' }}">
                                     Catalogue Others
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.catalogue-certifications.index') }}" class="nav-link {{ (request()->routeIs('admin.catalogue-certifications.index') || request()->routeIs('admin.catalogue-certifications.edit')) ? 'active' : '' }}">
                                     Catalogue Certifications
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.exam-overrides.index') }}" class="nav-link {{ request()->routeIs('admin.exam-overrides.*') ? 'active' : '' }}">
                                     Exam Overrides
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.certificate-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.certificate-settings.*') ? 'active' : '' }}">
                                     Certificate Settings
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 <!-- Membership Menu -->
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.members.*') || request()->routeIs('admin.membership-packages.*') ? '' : 'collapsed' }}" href="#sidebarMembership" data-bs-toggle="collapse"
                         role="button" aria-expanded="{{ request()->routeIs('admin.members.*') || request()->routeIs('admin.membership-packages.*') ? 'true' : 'false' }}" aria-controls="sidebarMembership">
                         <i class="ri-user-star-line"></i> <span>Membership</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.members.*') || request()->routeIs('admin.membership-packages.*') ? 'show' : '' }}" id="sidebarMembership">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                                     Members List
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.membership-packages.index') }}" class="nav-link {{ request()->routeIs('admin.membership-packages.index') ? 'active' : '' }}">
                                     All Packages
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                  <!-- Orders Menu -->
                  <li class="nav-item">
                      <a class="nav-link menu-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.refund-requests') ? '' : 'collapsed' }}" href="#sidebarOrders"
                          data-bs-toggle="collapse" role="button"
                          aria-expanded="{{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.refund-requests') ? 'true' : 'false' }}" aria-controls="sidebarOrders">
                          <i class="ri-shopping-cart-2-line"></i> <span>Orders</span>
                      </a>
                      <div class="collapse menu-dropdown {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.refund-requests') ? 'show' : '' }}" id="sidebarOrders">
                          <ul class="nav nav-sm flex-column">
                              <li class="nav-item">
                                  <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                                      Order List
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('admin.orders.refund-requests') }}" class="nav-link {{ request()->routeIs('admin.orders.refund-requests') ? 'active' : '' }}">
                                      Refund Requests
                                  </a>
                              </li>
                          </ul>
                      </div>
                  </li>

                 {{-- Communications Menu --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.contact-messages.*') || request()->routeIs('admin.certification-applications.*') || request()->routeIs('admin.advisory-requests.*') || request()->routeIs('admin.accreditation-applications.*') || request()->routeIs('admin.ce-activities.*') || request()->routeIs('admin.email-logs.*') ? '' : 'collapsed' }}" href="#sidebarCommunications"
                         data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.contact-messages.*') || request()->routeIs('admin.certification-applications.*') || request()->routeIs('admin.advisory-requests.*') || request()->routeIs('admin.accreditation-applications.*') || request()->routeIs('admin.ce-activities.*') || request()->routeIs('admin.email-logs.*') ? 'true' : 'false' }}" aria-controls="sidebarCommunications">
                         <i class="ri-pages-line"></i> <span>Communications</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.contact-messages.*') || request()->routeIs('admin.certification-applications.*') || request()->routeIs('admin.advisory-requests.*') || request()->routeIs('admin.accreditation-applications.*') || request()->routeIs('admin.ce-activities.*') || request()->routeIs('admin.email-logs.*') ? 'show' : '' }}" id="sidebarCommunications">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.contact-messages.index') }}" class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                                     Contact Messages
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.certification-applications.index') }}" class="nav-link {{ request()->routeIs('admin.certification-applications.*') ? 'active' : '' }}">
                                     Certification Applications
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.ce-activities.index') }}" class="nav-link {{ request()->routeIs('admin.ce-activities.*') ? 'active' : '' }}">
                                     CE Activities
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.advisory-requests.index') }}" class="nav-link {{ request()->routeIs('admin.advisory-requests.*') ? 'active' : '' }}">
                                     Advisory Requests
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.accreditation-applications.index') }}" class="nav-link {{ request()->routeIs('admin.accreditation-applications.*') ? 'active' : '' }}">
                                     Accreditation Applications
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.email-logs.index') }}" class="nav-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}">
                                     Email Logs
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- Settings --}}
                 <li class="menu-title"><span data-key="t-menu">Settings</span></li>

                 {{-- Settings Section --}}
                 <li class="nav-item">
                      <a class="nav-link menu-link {{ request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.managers.*') || request()->routeIs('admin.social-settings.*') || request()->routeIs('admin.api-settings.*') || request()->routeIs('admin.admin-settings.*') || request()->routeIs('admin.website-settings.*') || request()->routeIs('admin.mail-settings.*') ? '' : 'collapsed' }}"
                          href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                          aria-expanded="{{ request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.managers.*') || request()->routeIs('admin.social-settings.*') || request()->routeIs('admin.api-settings.*') || request()->routeIs('admin.admin-settings.*') || request()->routeIs('admin.website-settings.*') || request()->routeIs('admin.mail-settings.*') ? 'true' : 'false' }}"
                          aria-controls="sidebarSettings">
                          <i class="ri-settings-3-line"></i> <span>Settings</span>
                      </a>

                      <div class="collapse menu-dropdown {{ request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.managers.*') || request()->routeIs('admin.social-settings.*') || request()->routeIs('admin.api-settings.*') || request()->routeIs('admin.admin-settings.*') || request()->routeIs('admin.website-settings.*') || request()->routeIs('admin.mail-settings.*') ? 'show' : '' }}"
                         id="sidebarSettings">

                         <ul class="nav nav-sm flex-column">
                             {{-- Profile Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.profile-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.profile-settings.*') ? 'active' : '' }}">
                                     <i class="ri-user-settings-line"></i> <span>Profile Settings</span>
                                 </a>
                             </li>

                             {{-- Manage Managers --}}
                             @if (auth()->user()->role == 'admin')
                                 <li class="nav-item">
                                     <a href="{{ route('admin.managers.index') }}" class="nav-link {{ request()->routeIs('admin.managers.*') ? 'active' : '' }}">
                                         <i class="ri-group-line"></i> <span>Manage Managers</span>
                                     </a>
                                 </li>
                             @endif

                             {{-- Social Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.social-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.social-settings.*') ? 'active' : '' }}">
                                     <i class="ri-share-line"></i> <span>Social Settings</span>
                                 </a>
                             </li>

                             {{-- Stripe Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.api-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.api-settings.*') ? 'active' : '' }}">
                                     <i class="ri-mail-settings-line"></i> <span>Api Settings</span>
                                 </a>
                             </li>

                             {{-- Website Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.website-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.website-settings.*') ? 'active' : '' }}">
                                     <i class="ri-settings-3-line"></i> <span>Website Settings</span>
                                 </a>
                             </li>

                             {{-- Admin Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.admin-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.admin-settings.*') ? 'active' : '' }}">
                                     <i class="ri-settings-3-line"></i> <span>Admin Settings</span>
                                 </a>
                             </li>

                             {{-- Mail Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.mail-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.mail-settings.*') ? 'active' : '' }}">
                                     <i class="ri-mail-settings-line"></i> <span>Mail Settings</span>
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

             </ul>
         </div>
         <!-- Sidebar -->
     </div>

     <div class="sidebar-background"></div>
 </div>
