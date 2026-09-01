import { useEffect, useRef, type CSSProperties } from "react"
import { Outlet, useLocation } from "react-router"

import { DashboardHeader } from "@/components/dashboard/DashboardHeader"
import { DashboardSidebar } from "@/components/dashboard/DashboardSidebar"
import { SidebarInset, SidebarProvider } from "@/components/ui/sidebar"
import { TooltipProvider } from "@/components/ui/tooltip"

const DashboardLayout = () => {
  const { pathname } = useLocation()
  const contentRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    contentRef.current?.scrollTo({ top: 0, left: 0, behavior: "auto" })
  }, [pathname])

  return (
    <TooltipProvider>
      <SidebarProvider
        style={
          {
            "--sidebar-width": "14rem",
            "--sidebar": "#14392f",
            "--sidebar-foreground": "#d8e4df",
            "--sidebar-accent": "rgba(255, 255, 255, 0.08)",
            "--sidebar-accent-foreground": "#ffffff",
            "--sidebar-primary": "#ddb737",
            "--sidebar-primary-foreground": "#14392f",
          } as CSSProperties
        }
      >
        <DashboardSidebar />
        <SidebarInset className="h-screen min-h-0 overflow-hidden bg-muted/20">
          <DashboardHeader />
          <div
            ref={contentRef}
            data-dashboard-scroll
            className="min-h-0 flex-1 overflow-x-hidden overflow-y-auto"
          >
            <Outlet />
          </div>
        </SidebarInset>
      </SidebarProvider>
    </TooltipProvider>
  )
}

export default DashboardLayout
