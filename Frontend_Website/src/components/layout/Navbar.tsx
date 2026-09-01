import { useAppDispatch, useAppSelector } from "@/app/hooks"
import Logo from "@/assets/icons/MainLogo.svg?react"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle,
} from "@/components/ui/navigation-menu"
import { useLogoutMutation } from "@/features/auth/api/authApi"
import { useGetMenuCataloguesQuery } from "@/features/catalogue/api/catalogueApi"
import { logout } from "@/features/auth/store/authSlice"
import { cn } from "@/lib/utils"
import { ChevronDown, UserRound } from "lucide-react"
import * as React from "react"
import { Link, useNavigate } from "react-router"
import { toast } from "sonner"

interface NavChild {
  title: string
  href: string
  description?: string
}

interface NavGroup {
  heading: string
  items: { title: string; href: string }[]
}

interface LearningSection {
  heading: string
  items: { title: string; href: string }[]
}

interface LearningColumn {
  sections: LearningSection[]
}

interface NavItem {
  label: string
  href?: string
  children?: NavChild[]
  groups?: NavGroup[]
}

// Dynamic sections will be created inside the component using the API data

const learningVerticalBarClass =
  "absolute left-0 top-0 bottom-0 hidden w-px bg-linear-to-b from-transparent via-[#c0a062] to-transparent md:block"
const learningHorizontalBarClass =
  "absolute -top-3 left-0 h-px w-full bg-linear-to-r from-transparent via-[#c0a062]/50 to-transparent"

const navItems: NavItem[] = [
  {
    label: "CERTIFICATIONS",
    children: [
      {
        title: "CPHQ Certification",
        href: "/certifications/cphq",
        description: "Certified Professional in Healthcare Quality",
      },
      {
        title: "Exam Preparation",
        href: "/certifications/prep",
        description: "Study materials and practice exams",
      },
      {
        title: "Renewal",
        href: "/certifications/renewal",
        description: "Maintain your certification status",
      },
    ],
  },
  {
    label: "LEARNING",
  },
  {
    label: "ACCREDITATION",
    children: [
      { title: "Overview", href: "/accreditation" },
      { title: "Apply for Accreditation", href: "/accreditation/apply" },
    ],
  },
  {
    label: "ADVISORY",
    children: [
      { title: "Advisory Services", href: "/advisory" },
      {
        title: "Request Advisory Consultation",
        href: "/advisory/request-consultation",
      },
    ],
  },
  { label: "MEMBERSHIP", href: "/membership" },
  {
    label: "ABOUT",
    groups: [
      {
        heading: "GIHQS",
        items: [
          { title: "About the Institute", href: "/about/institute" },
          { title: "Mission, Vision & Values", href: "/about/mission" },
          { title: "Policies & Governance", href: "/about/policies" },
        ],
      },
      {
        heading: "Governance",
        items: [
          { title: "Strategic Advisory Board", href: "/about/advisory-board" },
          { title: "Accreditation Review Panel", href: "/about/review-panel" },
        ],
      },
      {
        heading: "Connect",
        items: [{ title: "Contact", href: "/contact" }],
      },
    ],
  },
]

interface ListItemProps extends React.ComponentPropsWithoutRef<"a"> {
  title: string
}

const ListItem = React.forwardRef<React.ComponentRef<"a">, ListItemProps>(
  ({ className, title, children, ...props }, ref) => (
    <li>
      <NavigationMenuLink asChild>
        <a
          ref={ref}
          className={cn(
            "block space-y-1 rounded-md p-3 leading-none no-underline transition-colors outline-none select-none",
            "hover:bg-[#1a5f4a]/5",
            "focus:bg-[#1a5f4a]/5",
            className
          )}
          {...props}
        >
          <div className="text-sm leading-none font-medium">{title}</div>
          {children && (
            <p className="line-clamp-2 text-sm leading-snug text-muted-foreground">
              {children}
            </p>
          )}
        </a>
      </NavigationMenuLink>
    </li>
  )
)
ListItem.displayName = "ListItem"

// ─── Trigger style shared ───
const triggerClass = cn(
  "h-auto bg-transparent px-3 py-2",
  "text-[0.75rem] font-semibold tracking-wide text-gray-600",
  "hover:bg-transparent hover:text-[#1a5f4a]",
  "focus:bg-transparent",
  "data-[state=open]:text-[#1a5f4a]",
  "data-[active]:text-[#1a5f4a]"
)

export function GIHQSNavbar() {
  const { user, token } = useAppSelector((state) => state.auth)
  const dispatch = useAppDispatch()
  const navigate = useNavigate()
  const [logoutUser] = useLogoutMutation()

  const { data: learningMenuData } = useGetMenuCataloguesQuery({
    service_type: "module,course,toolkit,webinar",
  })
  const catalogues = learningMenuData?.data

  const { data: certMenuData } = useGetMenuCataloguesQuery({
    service_type: "certification",
  })
  const certCatalogues = certMenuData?.data?.certifications || []

  const dynamicLearningSections: LearningSection[] = catalogues
    ? [
        {
          heading: "Healthcare Quality Improvement",
          items: (catalogues.modules.healthcare_quality_improvement || []).map(
            (item) => ({
              title: item.title,
              href: `/professional-development-catalogue/${item.id}`,
            })
          ),
        },
        {
          heading: "Patient Safety & Risk Management",
          items: (catalogues.modules.patient_safety_risk_management || []).map(
            (item) => ({
              title: item.title,
              href: `/professional-development-catalogue/${item.id}`,
            })
          ),
        },
        {
          heading: "Modules",
          items: (catalogues.modules.others || []).map((item) => ({
            title: item.title,
            href: `/professional-development-catalogue/${item.id}`,
          })),
        },
        {
          heading: "Courses",
          items: (catalogues.courses || []).map((item) => ({
            title: item.title,
            href: `/professional-development-catalogue/${item.id}`,
          })),
        },
        {
          heading: "Toolkits",
          items: (catalogues.toolkits || []).map((item) => ({
            title: item.title,
            href: `/professional-development-catalogue/${item.id}`,
          })),
        },
        {
          heading: "Webinars",
          items: (catalogues.webinars || []).map((item) => ({
            title: item.title,
            href: `/professional-development-catalogue/${item.id}`,
          })),
        },
        {
          heading: "Workshops",
          items: (catalogues.workshops || []).map((item) => ({
            title: item.title,
            href: `/professional-development-catalogue/${item.id}`,
          })),
        },
      ].filter((section) => section.items.length > 0)
    : []

  const col1Sections: LearningSection[] = []
  const col2Sections: LearningSection[] = []
  dynamicLearningSections.forEach((section, index) => {
    if (index % 2 === 0) {
      col1Sections.push(section)
    } else {
      col2Sections.push(section)
    }
  })
  const dynamicLearningColumns: LearningColumn[] = [
    { sections: col1Sections },
    { sections: col2Sections },
  ]

  const handleLogout = async () => {
    try {
      if (token) {
        const response = (await logoutUser().unwrap()) as {
          success?: boolean
          status?: boolean
          code?: number
          message?: string
        }
        if (
          response.success ||
          response.status === true ||
          response.code === 200
        ) {
          toast.success(response.message || "Successfully logged out")
        } else {
          toast.success("Successfully logged out")
        }
      } else {
        toast.success("Successfully logged out")
      }
    } catch (error) {
      console.error("Logout API failed:", error)
      toast.success("Successfully logged out") // Even if API fails (e.g. network), they are logged out locally
    } finally {
      dispatch(logout())
      navigate("/login")
    }
  }

  return (
    <header className="sticky top-0 z-50 w-full bg-[#F7FAF9]">
      <div className="container mx-auto flex h-28.5 items-center justify-between px-4 sm:px-6 lg:px-8">
        <Link
          to="/"
          aria-label="Go to home"
          className="w-32 shrink-0 sm:w-40 md:w-auto"
        >
          <Logo className="h-auto w-full md:w-[initial]" />
        </Link>

        {/* Center Navigation */}
        <NavigationMenu className="hidden lg:flex" viewport={false}>
          <NavigationMenuList className="gap-1">
            {navItems.map((item) =>
              item.label === "CERTIFICATIONS" ? (
                <NavigationMenuItem key={item.label} className="relative">
                  <NavigationMenuTrigger className={triggerClass}>
                    {item.label}
                  </NavigationMenuTrigger>
                  <NavigationMenuContent
                    className="absolute top-full left-1/2 mt-2 -translate-x-1/2 rounded-2xl bg-white p-0 shadow-[0_18px_40px_rgba(26,95,74,0.12)]"
                    style={{ width: "525px", maxWidth: "calc(100vw - 2rem)" }}
                  >
                    <div className="relative pr-2 pl-14">
                      <div className="absolute top-0 bottom-0 left-6 w-[0.3px] bg-linear-to-b from-transparent via-[#c0a062]/70 to-transparent" />

                      {certCatalogues.map((cert, index) => (
                        <section
                          key={cert.id}
                          className={cn(
                            "py-6 pr-1 pl-0",
                            index > 0 && "relative"
                          )}
                        >
                          {index > 0 && (
                            <div className="absolute -top-3 left-0 h-px w-full bg-linear-to-r from-transparent via-[#c0a062]/50 to-transparent" />
                          )}
                          <h4 className="text-xl font-bold text-[#b89551] uppercase">
                            {cert.short_title}
                          </h4>
                          <p className="mt-3 text-[0.95rem] leading-snug text-[#0f6b62]">
                            {cert.title}
                          </p>
                          <div className="mt-4 flex flex-nowrap gap-6 text-[0.75rem] font-medium tracking-[0.04em] text-[#b89551] uppercase">
                            <NavigationMenuLink asChild>
                              <a
                                href={`/professional-development-catalogue/${cert.id}`}
                                className="whitespace-nowrap transition-colors hover:text-[#8f671e]"
                              >
                                Explore
                              </a>
                            </NavigationMenuLink>
                            <NavigationMenuLink asChild>
                              <a
                                href={`/professional-development-catalogue/${cert.id}/story-guide`}
                                className="whitespace-nowrap transition-colors hover:text-[#8f671e]"
                              >
                                Story Guide
                              </a>
                            </NavigationMenuLink>
                          </div>
                        </section>
                      ))}
                    </div>
                  </NavigationMenuContent>
                </NavigationMenuItem>
              ) : item.label === "LEARNING" ? (
                <NavigationMenuItem key={item.label} className="relative">
                  <NavigationMenuTrigger className={triggerClass}>
                    {item.label}
                  </NavigationMenuTrigger>
                  <NavigationMenuContent className="absolute top-full left-1/2 mt-2 w-[min(56rem,calc(100vw-2rem))]! max-w-none! -translate-x-1/2 rounded-lg border bg-white p-6 shadow-lg">
                    <div className="grid min-w-0 gap-6 md:grid-cols-2 md:gap-x-10">
                      {dynamicLearningColumns.map((column, columnIndex) => (
                        <div
                          key={columnIndex}
                          className={cn(
                            "relative min-w-0 space-y-6 pl-6",
                            columnIndex === 1 && "md:pl-6"
                          )}
                        >
                          <div
                            className={cn(
                              learningVerticalBarClass,
                              columnIndex === 1 && "md:-left-3"
                            )}
                          />

                          {column.sections.map((section, sectionIndex) => (
                            <section
                              key={section.heading}
                              className="relative space-y-3 pb-5 last:pb-0"
                            >
                              {sectionIndex > 0 && (
                                <div className={learningHorizontalBarClass} />
                              )}
                              <h4
                                className={cn(
                                  "text-[0.9rem] font-bold tracking-[0.08em] text-[#b89551] uppercase"
                                )}
                              >
                                {section.heading}
                              </h4>
                              <ul className="space-y-3">
                                {section.items.map((link) => (
                                  <li key={link.title}>
                                    <NavigationMenuLink asChild>
                                      <a
                                        href={link.href}
                                        className={cn(
                                          "block text-[0.95rem] leading-snug font-medium text-[#1a5f4a] transition-colors hover:text-[#145240] hover:underline"
                                        )}
                                      >
                                        {link.title}
                                      </a>
                                    </NavigationMenuLink>
                                  </li>
                                ))}
                              </ul>
                            </section>
                          ))}
                        </div>
                      ))}
                    </div>
                  </NavigationMenuContent>
                </NavigationMenuItem>
              ) : item.groups ? (
                <NavigationMenuItem key={item.label} className="relative">
                  <NavigationMenuTrigger className={triggerClass}>
                    {item.label}
                  </NavigationMenuTrigger>
                  <NavigationMenuContent className="center-x-0 absolute top-full left-1/2 mt-2 -translate-x-1/2 rounded-lg border bg-white p-6 shadow-lg">
                    <div className="relative w-80 space-y-6 pl-6">
                      {/* Left vertical bar - faded at ends */}
                      <div className="absolute top-0 bottom-0 left-0 w-px bg-linear-to-b from-transparent via-[#c0a062] to-transparent" />

                      {item.groups.map((group, idx) => (
                        <div key={group.heading} className="relative space-y-4">
                          {/* Horizontal divider - faded at ends */}
                          {idx > 0 && (
                            <div className="absolute -top-3 left-0 h-px w-full bg-linear-to-r from-transparent via-[#c0a062]/50 to-transparent" />
                          )}
                          <h4 className="text-xl font-bold text-[#b89551]">
                            {group.heading}
                          </h4>
                          <ul className="space-y-3">
                            {group.items.map((link) => (
                              <li key={link.title}>
                                <NavigationMenuLink asChild>
                                  <a
                                    href={link.href}
                                    className="text-[0.95rem] font-medium text-[#1a5f4a] transition-colors hover:text-[#145240] hover:underline"
                                  >
                                    {link.title}
                                  </a>
                                </NavigationMenuLink>
                              </li>
                            ))}
                          </ul>
                        </div>
                      ))}
                    </div>
                  </NavigationMenuContent>
                </NavigationMenuItem>
              ) : item.children ? (
                <NavigationMenuItem key={item.label} className="relative">
                  <NavigationMenuTrigger className={triggerClass}>
                    {item.label}
                  </NavigationMenuTrigger>
                  <NavigationMenuContent className="absolute top-full left-1/2 mt-2 -translate-x-1/2 rounded-lg bg-white p-0 shadow-[0_18px_40px_rgba(26,95,74,0.12)]">
                    <div className="relative w-72 py-2 pr-3 pl-6">
                      <div className="absolute top-0 bottom-0 left-0 w-px bg-linear-to-b from-transparent via-[#c0a062] to-transparent" />

                      <ul className="space-y-2">
                        {item.children.map((child) => (
                          <li key={child.title}>
                            <NavigationMenuLink asChild>
                              <a
                                href={child.href}
                                className="block text-[0.95rem] leading-snug font-medium text-[#b89551] transition-colors hover:text-[#8f671e] hover:underline"
                              >
                                {child.title}
                              </a>
                            </NavigationMenuLink>
                          </li>
                        ))}
                      </ul>
                    </div>
                  </NavigationMenuContent>
                </NavigationMenuItem>
              ) : (
                <NavigationMenuItem key={item.label}>
                  <NavigationMenuLink
                    href={item.href}
                    className={cn(navigationMenuTriggerStyle(), triggerClass)}
                  >
                    {item.label}
                  </NavigationMenuLink>
                </NavigationMenuItem>
              )
            )}
          </NavigationMenuList>
        </NavigationMenu>

        {/* Right — User Profile */}
        {token && user ? (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button
                variant="ghost"
                className="h-10 gap-2 rounded-full border border-[#1a5f4a]/20 bg-[#1a5f4a] pl-1.5 text-white hover:bg-[#145240] hover:text-white focus-visible:ring-[#1a5f4a]"
              >
                <Avatar className="h-8 w-8 border border-white/30">
                  <AvatarImage src={user.avatar || undefined} alt={user.name} />
                  <AvatarFallback className="bg-[#145240] text-[10px] text-white uppercase">
                    {user.name ? user.name.substring(0, 2) : "US"}
                  </AvatarFallback>
                </Avatar>
                <span className="text-sm font-medium">{user.name}</span>
                <ChevronDown className="h-4 w-4 opacity-70" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-56">
              <DropdownMenuItem onClick={() => navigate("/dashboard/profile")}>
                Profile
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => navigate("/dashboard")}>
                Dashboard
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => navigate("/dashboard/courses")}>
                My Courses
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={handleLogout}
                className="cursor-pointer text-red-600 focus:text-red-600"
              >
                Sign Out
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ) : (
          <Link to="/login">
            <Button
              variant="ghost"
              className="h-10 gap-2 rounded-full border border-[#1a5f4a]/20 bg-[#1a5f4a] px-6 text-white hover:bg-[#145240] hover:text-white focus-visible:ring-[#1a5f4a]"
            >
              <span className="text-sm font-medium">Log In</span>
              <UserRound className="h-4 w-4" />
            </Button>
          </Link>
        )}
      </div>
    </header>
  )
}
