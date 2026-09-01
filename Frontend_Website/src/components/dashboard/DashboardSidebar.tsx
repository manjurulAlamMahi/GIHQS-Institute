import { NavLink, useLocation, useNavigate } from "react-router"
import { LogOut } from "lucide-react"

import { useAppDispatch } from "@/app/hooks"
import { useLogoutMutation } from "@/features/auth/api/authApi"
import { logout } from "@/features/auth/store/authSlice"
import { ROUTES } from "@/routes/routes.constants"

import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from "@/components/ui/sidebar"
import { cn } from "@/lib/utils"

import { dashboardSidebarNavItems } from "./dashboard-sidebar-nav"

function DashboardLogo() {
  return (
    <a href="/" className="mx-auto block w-36.5 text-center text-white">
      <span className="block font-serif text-[40px] leading-[0.82] tracking-[-0.02em]">
        GIHQS
      </span>
      <span className="mt-1 block text-[5px] font-semibold uppercase leading-none tracking-[0.24em]">
        Global Institute For
      </span>
      <span className="mt-px block text-[5px] font-semibold uppercase leading-none tracking-[0.18em]">
        Healthcare Quality &amp; Safety
      </span>
      <span className="mx-auto mt-1.25 block h-px w-28.5 bg-white/80" />
      <span className="mx-auto -mt-0.5 block size-1 rounded-full bg-white" />
    </a>
  )
}

export function DashboardSidebar() {
  const { pathname } = useLocation()
  const { setOpenMobile } = useSidebar()
  const navigate = useNavigate()
  const dispatch = useAppDispatch()
  const [logoutApi, { isLoading }] = useLogoutMutation()

  const handleLogout = async () => {
    try {
      await logoutApi().unwrap()
    } catch (error) {
      console.error("Logout failed", error)
    } finally {
      dispatch(logout())
      navigate(ROUTES.LOGIN)
    }
  }

  return (
    <Sidebar className="border-r-0 bg-[#14392f] text-[#d8e4df] shadow-none">
      <SidebarHeader className="px-3 pb-6 pt-4">
        <DashboardLogo />
      </SidebarHeader>

      <SidebarContent>
        <SidebarGroup className="px-3 py-0">
          <SidebarGroupContent>
            <SidebarMenu className="gap-1.25">
              {dashboardSidebarNavItems.map(({ label, to, icon: Icon, end }) => {
                const isActive = end ? pathname === to : pathname.startsWith(to)

                return (
                  <SidebarMenuItem key={label}>
                    <SidebarMenuButton
                      asChild
                      isActive={isActive}
                      className={cn(
                        "h-8 rounded-xs px-3 text-[15px] font-normal hover:bg-white/8 hover:text-white data-active:bg-[#ddb737] data-active:text-[#14392f]",
                        !isActive && "text-[#d8e4df]"
                      )}
                    >
                      <NavLink to={to} end={end} onClick={() => setOpenMobile(false)}>
                        <Icon
                          className="size-3.5 shrink-0 stroke-[1.8]"
                          aria-hidden="true"
                        />
                        <span>{label}</span>
                      </NavLink>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                )
              })}

              <SidebarMenuItem className="pt-2.25">
                <SidebarMenuButton 
                  onClick={handleLogout}
                  disabled={isLoading}
                  className="h-8 rounded-lg px-3 text-[15px] font-normal text-[#ff6658] hover:bg-white/8 hover:text-[#ff6658]"
                >
                  <LogOut
                    className="size-3.5 shrink-0 stroke-[1.8]"
                    aria-hidden="true"
                  />
                  <span>{isLoading ? "Logging out..." : "Logout"}</span>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
    </Sidebar>
  )
}
