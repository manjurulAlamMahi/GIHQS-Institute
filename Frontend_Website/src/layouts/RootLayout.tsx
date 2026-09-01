import Footer from "@/components/layout/Footer"
import { GIHQSNavbar } from "@/components/layout/Navbar"
import { CookieConsent } from "@/components/layout/CookieConsent"
import { Outlet, useLocation, matchPath } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

export default function RootLayout() {
  const location = useLocation();

  const courseViewerRoutes = [
    ROUTES.COURSE_DETAIL,
    ROUTES.COURSE_STORY_GUIDE,
    ROUTES.FULL_MODULE,
  ];

  const isCoursePage = courseViewerRoutes.some(route => 
    matchPath({ path: route, end: true }, location.pathname)
  );

  return (
    <div className="min-h-screen flex flex-col">
      {isCoursePage && (
        <style>
          {`
            html, body {
              scrollbar-width: none !important;
              -ms-overflow-style: none !important;
            }
            html::-webkit-scrollbar, body::-webkit-scrollbar {
              display: none !important;
            }
          `}
        </style>
      )}
      <GIHQSNavbar />
      <main className="flex-1">
        <Outlet />
      </main>
      <Footer />
      <CookieConsent />
    </div>
  )
}
