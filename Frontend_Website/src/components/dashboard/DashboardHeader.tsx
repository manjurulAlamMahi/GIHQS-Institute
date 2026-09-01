import type { RootState } from "@/app/rootReducer"
import { useEffect } from "react"
import { useSelector } from "react-redux"
import { useLocation } from "react-router"

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { SidebarTrigger } from "@/components/ui/sidebar"
import { ROUTES } from "@/routes/routes.constants"

const pageHeaders = {
  [ROUTES.DASHBOARD]: {
    title: "Welcome back",
    subtitle:
      "Your GIHQS certification, course, CE, accreditation, and account overview.",
  },
  [ROUTES.DASHBOARD_PROFESSIONAL_DEVELOPMENT]: {
    title: "Professional Development",
    subtitle:
      "Featured items should be selected dynamically from admin using a featured toggle.",
  },
  [ROUTES.DASHBOARD_APPLY_CERTIFICATION]: {
    title: "Apply for Certification",
    subtitle:
      "Complete your certification application and submit it for eligibility review.",
  },
  [ROUTES.DASHBOARD_CERTIFICATIONS]: {
    title: "My Certifications",
    subtitle:
      "This page should help the user quickly see progress, CE eligibility, exam attempts, and the next action required.",
  },
  [ROUTES.DASHBOARD_COURSES]: {
    title: "My Courses",
    subtitle:
      "This page should help the user quickly see progress, CE eligibility, exam attempts, and the next action required.",
  },
  // [ROUTES.DASHBOARD_SCHEDULES]: {
  //   title: "My Schedules",
  //   subtitle:
  //     "This page should help the user quickly see progress, CE eligibility, exam attempts, and the next action required.",
  // },
  [ROUTES.DASHBOARD_CE_TRACKER]: {
    title: "CE Tracker",
    subtitle:
      "Monitor progress across all multiple area certifications and domains. Each card contains basic cycle renewal timing, reporting lists, and CE hours you have on file for a certificate cycle or domain.",
  },
  [ROUTES.DASHBOARD_ACCREDITATION]: {
    title: "Accreditation Portal",
    subtitle:
      "Manage the full accreditation record here, including stage progress, reviewer feedback, requested evidence, uploaded documents, and final decision workflow.",
  },
  [ROUTES.DASHBOARD_ORDERS_PAYMENTS]: {
    title: "Orders & Payments",
    subtitle: "",
  },
  [ROUTES.DASHBOARD_MEMBERSHIP]: {
    title: "Membership",
    subtitle: "Manage your plan and unlock more value",
  },
  [ROUTES.DASHBOARD_PROFILE]: {
    title: "Profile Settings",
    subtitle: "Manage your plan and unlock more value",
  },
} as const

export function DashboardHeader() {
  const { pathname } = useLocation()
  const user = useSelector((state: RootState) => state.auth.user)
  const header =
    pageHeaders[pathname as keyof typeof pageHeaders] ??
    pageHeaders[ROUTES.DASHBOARD]

  const displayTitle =
    pathname === ROUTES.DASHBOARD
      ? `${header.title}, ${user?.name?.split(" ")[0] || "User"}`
      : header.title

  useEffect(() => {
    document.title = `${displayTitle} | GIHQS`
  }, [displayTitle])

  return (
    <header className="sticky top-0 z-10 flex min-h-14 items-center justify-between border-b border-border bg-white px-4 py-3 md:min-h-18 md:px-6 md:py-4">
      <div className="flex items-center gap-3">
        <SidebarTrigger className="flex size-9 rounded-[8px] border border-border bg-white text-[#14392f] shadow-xs hover:bg-muted md:hidden" />

        <div className="hidden md:block">
          <h1 className="font-heading text-[26px] leading-tight font-semibold text-[#14392f]">
            {displayTitle}
          </h1>
          {header.subtitle && (
            <p className="mt-1 max-w-4xl text-[13px] text-muted-foreground">
              {header.subtitle}
            </p>
          )}
        </div>
      </div>

      <button
        type="button"
        className="inline-flex h-9 items-center gap-2 rounded-full border border-border bg-white px-2.5 pr-3 text-sm font-medium text-[#14392f] shadow-xs transition-colors hover:bg-muted/60"
      >
        <Avatar className="-ml-1 size-6">
          <AvatarImage src={user?.avatar || undefined} alt={user?.name} />
          <AvatarFallback className="bg-[#ddb737] text-[10px] font-semibold text-[#14392f] uppercase">
            {user?.name ? user.name.substring(0, 2) : "US"}
          </AvatarFallback>
        </Avatar>
        <span className="hidden sm:inline">{user?.name || "User"}</span>
      </button>
    </header>
  )
}
