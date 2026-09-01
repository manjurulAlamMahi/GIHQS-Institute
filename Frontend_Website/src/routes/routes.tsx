import { Navigate, createBrowserRouter } from "react-router"
import { ROUTES } from "./routes.constants"
import RootLayout from "@/layouts/RootLayout"
import PrivateRoute from "./PrivateRoute"
import ScrollToTop from "./ScrollToTop"
import DashboardLayout from "@/layouts/DashboardLayout"
import AccreditationPage from "@/pages/Accreditation/Index"
import ApplyAccreditationPage from "@/pages/ApplyAccreditation/Index"
import AboutInstitutePage from "@/pages/About/Institute/Index"
import MissionVisionValuesPage from "@/pages/About/Mission/Index"
import PoliciesGovernancePage from "@/pages/About/Policies/Index"
import StrategicAdvisoryBoardPage from "@/pages/About/AdvisoryBoard/Index"
import AccreditationReviewPanelPage from "@/pages/About/ReviewPanel/Index"
import ContactPage from "@/pages/Contact/Index"
import NotFoundPage from "@/pages/NotFound"
import HomePage from "@/pages/Home/Index"
import MembershipPage from "@/pages/Membership/Index"
import LoginPage from "@/pages/Login/Login"
import SignupPage from "@/pages/Signup/Signup"
import VerifyEmailPage from "@/pages/VerifyEmail/VerifyEmail"
import ForgotPasswordPage from "@/pages/ForgotPassword/ForgotPassword"
import ProfessionalDevelopmentCataloguePage from "@/pages/ProfessionalDevelopmentCatalogue/Index"
import CourseDetails from "@/pages/CourseDetails/Index"
import CourseStoryGuide from "@/pages/CourseStoryGuide/Index"
import DashboardOverviewPage from "@/pages/Dashboard/Overview/Index"
import ProfessionalDevelopmentPage from "@/pages/Dashboard/ProfessionalDevelopment/Index"
import ApplyCertificationPage from "@/pages/Dashboard/ApplyCertification/Index"
import MyCertificationsPage from "@/pages/Dashboard/MyCertifications/Index"
import CeTrackerPage from "@/pages/Dashboard/CeTracker/Index"
import DashboardAccreditationPage from "@/pages/Dashboard/Accreditation/Index"
import AdvisoryRequestsPage from "@/pages/Dashboard/AdvisoryRequests/Index"
import OrdersPaymentsPage from "@/pages/Dashboard/OrdersPayments/Index"
import DashboardMembershipPage from "@/pages/Dashboard/Membership/Index"
import ProfilePage from "@/pages/Dashboard/Profile/Index"
import SubscriptionPage from "@/pages/Dashboard/Subscription/Index"
import FullModule from "@/pages/FullModule/Index"
import Advisory from "@/pages/Advisory/Index"
import RequestAdvisoryConsultationPage from "@/pages/RequestAdvisoryConsultation/Index"
import CertificationPage from "@/pages/Certification/Index"
import PaymentSuccessPage from "@/pages/Payment/Success"
import PaymentCancelPage from "@/pages/Payment/Cancel"
import DashboardCourseDetail from "@/pages/Dashboard/MyCourseDetail/Index"
import MyCoursesPage from "@/pages/Dashboard/MyCourses/Index"
import DashboardExamPage from "@/pages/Dashboard/Exam/Index"
import DashboardHtmlResourcePage from "@/pages/Dashboard/HtmlResource/Index"
import OtherPage from "@/pages/OtherPages/Index"

export const router = createBrowserRouter([
  {
    element: <ScrollToTop />,
    children: [
      { path: ROUTES.LOGIN, element: <LoginPage /> },
      { path: ROUTES.SIGNUP, element: <SignupPage /> },
      { path: ROUTES.VERIFY_EMAIL, element: <VerifyEmailPage /> },
      { path: ROUTES.FORGOT_PASSWORD, element: <ForgotPasswordPage /> },
      { path: ROUTES.PAYMENT_SUCCESS, element: <PaymentSuccessPage /> },
      { path: ROUTES.PAYMENT_CANCEL, element: <PaymentCancelPage /> },
      {
        element: <PrivateRoute />,
        children: [
          {
            path: ROUTES.DASHBOARD,
            element: <DashboardLayout />,
            children: [
              { index: true, element: <DashboardOverviewPage /> },
              {
                path: ROUTES.DASHBOARD_PROFESSIONAL_DEVELOPMENT,
                element: <ProfessionalDevelopmentPage />,
              },
              {
                path: ROUTES.DASHBOARD_APPLY_CERTIFICATION,
                element: <Navigate to={ROUTES.APPLY_CERTIFICATION} replace />,
              },
              {
                path: ROUTES.DASHBOARD_CERTIFICATIONS,
                element: <MyCertificationsPage />,
              },
              { path: ROUTES.DASHBOARD_COURSES, element: <MyCoursesPage /> },
              {
                path: ROUTES.DASHBOARD_COURSE_DETAIL,
                element: <DashboardCourseDetail />,
              },
              { path: ROUTES.DASHBOARD_EXAM, element: <DashboardExamPage /> },
              {
                path: ROUTES.DASHBOARD_HTML_RESOURCE,
                element: <DashboardHtmlResourcePage />,
              },
              // { path: ROUTES.DASHBOARD_SCHEDULES, element: <MySchedulesPage /> },
              { path: ROUTES.DASHBOARD_CE_TRACKER, element: <CeTrackerPage /> },
              {
                path: ROUTES.DASHBOARD_ACCREDITATION,
                element: <DashboardAccreditationPage />,
              },
              {
                path: ROUTES.DASHBOARD_ADVISORY_REQUESTS,
                element: <AdvisoryRequestsPage />,
              },
              {
                path: ROUTES.DASHBOARD_ORDERS_PAYMENTS,
                element: <OrdersPaymentsPage />,
              },
              {
                path: ROUTES.DASHBOARD_MEMBERSHIP,
                element: <DashboardMembershipPage />,
              },
              { path: ROUTES.DASHBOARD_PROFILE, element: <ProfilePage /> },
              {
                path: ROUTES.DASHBOARD_SUBSCRIPTION,
                element: <SubscriptionPage />,
              },
            ],
          },
        ],
      },
      {
        element: <PrivateRoute />,
        children: [
          {
            element: <RootLayout />,
            children: [
              {
                path: ROUTES.APPLY_CERTIFICATION,
                element: <ApplyCertificationPage />,
              },
            ],
          },
        ],
      },
      {
        element: <RootLayout />,
        children: [
          { path: ROUTES.HOME, element: <HomePage /> },
          { path: ROUTES.ADVISORY, element: <Advisory /> },
          { path: ROUTES.ACCREDITATION, element: <AccreditationPage /> },
          {
            path: ROUTES.APPLY_ACCREDITATION,
            element: <ApplyAccreditationPage />,
          },
          { path: ROUTES.ABOUT_INSTITUTE, element: <AboutInstitutePage /> },
          { path: ROUTES.ABOUT_MISSION, element: <MissionVisionValuesPage /> },
          { path: ROUTES.ABOUT_POLICIES, element: <PoliciesGovernancePage /> },
          {
            path: ROUTES.ABOUT_ADVISORY,
            element: <StrategicAdvisoryBoardPage />,
          },
          {
            path: ROUTES.ABOUT_REVIEW,
            element: <AccreditationReviewPanelPage />,
          },
          { path: ROUTES.CONTACT, element: <ContactPage /> },
          { path: ROUTES.MEMBERSHIP, element: <MembershipPage /> },
          {
            path: ROUTES.REQUEST_ADVISORY_CONSULTATION,
            element: <RequestAdvisoryConsultationPage />,
          },
          { path: ROUTES.CERTIFICATION, element: <CertificationPage /> },
          {
            path: ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE,
            element: <ProfessionalDevelopmentCataloguePage />,
          },
          { path: ROUTES.COURSE_DETAIL, element: <CourseDetails /> },
          { path: ROUTES.COURSE_STORY_GUIDE, element: <CourseStoryGuide /> },
          { path: ROUTES.FULL_MODULE, element: <FullModule /> },
          { path: ROUTES.OTHER_PAGE, element: <OtherPage /> },
          { path: "*", element: <NotFoundPage /> },
        ],
      },
    ],
  },
])
