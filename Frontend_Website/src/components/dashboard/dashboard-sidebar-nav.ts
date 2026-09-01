import {
  BadgeCheck,
  BookOpen,
  BriefcaseBusiness,
  CreditCard,
  GraduationCap,
  LayoutDashboard,
  MessageSquare,
  ShoppingCart,
  User,
  Users,
} from "lucide-react"
import type { ComponentType, SVGProps } from "react"

import { ROUTES } from "@/routes/routes.constants"

export type DashboardSidebarIcon = ComponentType<SVGProps<SVGSVGElement>>

export type DashboardSidebarNavItem = {
  label: string
  to: string
  icon: DashboardSidebarIcon
  end?: boolean
}

export const dashboardSidebarNavItems: DashboardSidebarNavItem[] = [
  {
    label: "Dashboard",
    to: ROUTES.DASHBOARD,
    icon: LayoutDashboard,
    end: true,
  },
  {
    label: "Professional Development",
    to: ROUTES.DASHBOARD_PROFESSIONAL_DEVELOPMENT,
    icon: BriefcaseBusiness,
  },
  {
    label: "My Catalogues",
    to: ROUTES.DASHBOARD_COURSES,
    icon: BookOpen,
  },
  {
    label: "Certifications Requests",
    to: ROUTES.DASHBOARD_CERTIFICATIONS,
    icon: BadgeCheck,
  },
  // {
  //   label: "My Schedules",
  //   to: ROUTES.DASHBOARD_SCHEDULES,
  //   icon: CalendarDays,
  // },
  {
    label: "CE Tracker",
    to: ROUTES.DASHBOARD_CE_TRACKER,
    icon: GraduationCap,
  },
  {
    label: "Accreditation",
    to: ROUTES.DASHBOARD_ACCREDITATION,
    icon: BadgeCheck,
  },
  {
    label: "Advisory Requests",
    to: ROUTES.DASHBOARD_ADVISORY_REQUESTS,
    icon: MessageSquare,
  },
  {
    label: "Orders & Payments",
    to: ROUTES.DASHBOARD_ORDERS_PAYMENTS,
    icon: ShoppingCart,
  },
  {
    label: "Membership",
    to: ROUTES.DASHBOARD_MEMBERSHIP,
    icon: Users,
  },
  {
    label: "Subscription",
    to: ROUTES.DASHBOARD_SUBSCRIPTION,
    icon: CreditCard,
  },
  {
    label: "Profile",
    to: ROUTES.DASHBOARD_PROFILE,
    icon: User,
  },
]
