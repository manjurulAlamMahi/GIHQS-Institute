  <!-- Template css start -->

  <!-- jsvectormap css -->
  <link href="{{ asset('/') }}backend/assets/libs/jsvectormap/jsvectormap.min.css" rel="stylesheet" type="text/css" />

  <!--Swiper slider css-->
  <link href="{{ asset('/') }}backend/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

  <!-- Layout config Js -->
  <script src="{{ asset('/') }}backend/assets/js/layout.js"></script>
  <!-- Bootstrap Css -->
  <link href="{{ asset('/') }}backend/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="{{ asset('/') }}backend/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="{{ asset('/') }}backend/assets/css/app.min.css" rel="stylesheet" type="text/css" />
  <!-- custom Css-->
  <link href="{{ asset('/') }}backend/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

  <!-- Template css end -->

  <!-- Dropify CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" rel="stylesheet">
  <!--  Font awesome cdn-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.0.0/css/responsive.foundation.min.css" />

  <!-- Dropify css -->
  <style>
      /* Dropify message text fix */
      .dropify-wrapper .dropify-message p {
          font-size: 40px;
          line-height: 1.2;
          text-align: center;
          padding: 0 6px;
          margin: 0;
          word-break: break-word;
      }

      /* Icon size */
      .dropify-wrapper .dropify-message span.file-icon {
          font-size: 50px;
      }

      /* Small width support */
      @media (max-width: 420px) {
          .dropify-wrapper .dropify-message p {
              font-size: 14;
              line-height: 1.1;
          }
      }
  </style>

  <!-- ===== GIHQS Dark Forest Green Theme (light mode only) ===== -->
  <style>
      /* ========================================================
         Green sidebar ONLY applies in light/default mode.
         When dark mode is toggled (data-bs-theme="dark"),
         the sidebar falls back to Velzon's original dark colors.
         ======================================================== */
      /* ----- Sidebar Background ----- */
      html:not([data-bs-theme="dark"]) {
          --vz-vertical-menu-bg: #1a3c34 !important;
          --vz-vertical-menu-border: #1a3c34 !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .app-menu.navbar-menu,
      html:not([data-bs-theme="dark"]) .app-menu.navbar-menu {
          background-color: #1a3c34 !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .app-menu .navbar-brand-box,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-brand-box {
          background-color: #1a3c34 !important;
      }

      /* Sidebar background overlay */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .sidebar-background,
      html:not([data-bs-theme="dark"]) .sidebar-background {
          background-color: #1a3c34 !important;
      }

      /* ----- Collapsed/Small Sidebar Sub-menu Float Overrides ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar-size="sm"] .navbar-menu .navbar-nav .nav-item:hover > .menu-dropdown,
      html:not([data-bs-theme="dark"]) [data-sidebar-size="sm"] .navbar-menu .navbar-nav .nav-item:hover > a.menu-link,
      html:not([data-bs-theme="dark"]) [data-sidebar-size="sm-hover"] .navbar-menu .navbar-nav .nav-item:hover > .menu-dropdown,
      html:not([data-bs-theme="dark"]) [data-sidebar-size="sm-hover"] .navbar-menu .navbar-nav .nav-item:hover > a.menu-link,
      html:not([data-bs-theme="dark"]) [data-sidebar-size="sm-hover-active"] .navbar-menu .navbar-nav .nav-item:hover > .menu-dropdown,
      html:not([data-bs-theme="dark"]) [data-sidebar-size="sm-hover-active"] .navbar-menu .navbar-nav .nav-item:hover > a.menu-link {
          background-color: #1a3c34 !important;
          background: #1a3c34 !important;
      }

      /* ----- Sidebar Menu Links ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-link,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-nav .nav-link {
          color: rgba(255, 255, 255, 0.7) !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-link:hover,
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-link:focus,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-nav .nav-link:hover {
          color: #ffffff !important;
          background-color: rgba(255, 255, 255, 0.08) !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-link.active,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-nav .nav-link.active {
          color: #ffffff !important;
          background-color: rgba(255, 255, 255, 0.12) !important;
      }

      /* ----- Sidebar Sub-menu Links ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-sm .nav-link,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-nav .nav-sm .nav-link {
          color: rgba(255, 255, 255, 0.55) !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-sm .nav-link:hover,
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .nav-sm .nav-link.active,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-nav .nav-sm .nav-link:hover,
      html:not([data-bs-theme="dark"]) .app-menu .navbar-nav .nav-sm .nav-link.active {
          color: #ffffff !important;
      }

      /* ----- Menu Title / Section Headers ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .menu-title,
      html:not([data-bs-theme="dark"]) .app-menu .menu-title {
          color: rgba(255, 255, 255, 0.4) !important;
      }

      /* ----- Sidebar User Section ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .sidebar-user,
      html:not([data-bs-theme="dark"]) .sidebar-user {
          background-color: rgba(255, 255, 255, 0.05) !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .sidebar-user-name-text,
      html:not([data-bs-theme="dark"]) .sidebar-user-name-text {
          color: #ffffff !important;
      }

      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .sidebar-user-name-sub-text,
      html:not([data-bs-theme="dark"]) .sidebar-user-name-sub-text {
          color: rgba(255, 255, 255, 0.6) !important;
      }

      /* ----- Sidebar Scrollbar ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .app-menu .simplebar-scrollbar::before {
          background-color: rgba(255, 255, 255, 0.25) !important;
      }

      /* ----- Menu Dropdown (collapse) ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .menu-dropdown {
          background-color: rgba(0, 0, 0, 0.1) !important;
      }

      /* ----- Vertical Hover Button ----- */
      html:not([data-bs-theme="dark"]) #vertical-hover {
          color: rgba(255, 255, 255, 0.7) !important;
      }

      html:not([data-bs-theme="dark"]) #vertical-hover:hover {
          color: #ffffff !important;
      }

      /* ----- Menu Collapse Icon ----- */
      html:not([data-bs-theme="dark"]) [data-sidebar="dark"] .navbar-nav .menu-link::after {
          color: rgba(255, 255, 255, 0.5) !important;
      }

      /* ----- Accent colors (apply in both modes) ----- */
      .btn-success,
      .btn-primary,
      .btn-secondary,
      .bg-success {
          background-color: #1a3c34 !important;
          border-color: #1a3c34 !important;
      }

      .btn-success:hover,
      .btn-success:focus,
      .btn-success:active,
      .btn-primary:hover,
      .btn-primary:focus,
      .btn-primary:active,
      .btn-secondary:hover,
      .btn-secondary:focus,
      .btn-secondary:active {
          background-color: #264f44 !important;
          border-color: #264f44 !important;
      }

      .text-success {
          color: #1a3c34 !important;
      }

      .text-primary {
          color: #1a3c34 !important;
      }

      html:not([data-bs-theme="dark"]) .topbar-hamburger .btn {
          color: #1a3c34 !important;
      }
  </style>
