import { Skeleton } from "@/components/ui/skeleton"
import { useGetHomeServicesPathwaysQuery } from "@/features/home/api/homeApi"
import { ROUTES } from "@/routes/routes.constants"
import { ArrowRight } from "lucide-react"
import { Link } from "react-router"

export default function PathwayCTA() {
  const { data: response, isLoading } = useGetHomeServicesPathwaysQuery()
  const nextSteps = response?.data?.home_gihqs?.home_next_steps

  if (isLoading) {
    return (
      <div className="w-full bg-background px-0 py-16">
        <div className="relative grid grid-cols-1 overflow-hidden rounded-[24px] border-3 border-[#F0D070] bg-primary shadow-lg lg:grid-cols-12">
          {/* Skeleton Left Content Block */}
          <div className="relative z-10 flex flex-col justify-center p-8 md:p-12 lg:col-span-6 lg:p-16">
            <Skeleton className="mb-4 h-3 w-48 bg-white/20 md:h-4" />
            <Skeleton className="mb-2 h-10 w-full max-w-md bg-white/20 md:h-12 lg:h-14" />
            <Skeleton className="h-10 w-3/4 max-w-sm bg-white/20 md:h-12 lg:h-14" />
          </div>

          {/* Skeleton Right Actionable Rows */}
          <div className="relative z-10 flex flex-col justify-center space-y-3 border-white/10 bg-white/2 p-8 backdrop-blur-sm md:p-12 lg:col-span-6 lg:border-l lg:p-16">
            {Array.from({ length: 4 }).map((_, i) => (
              <div
                key={i}
                className="flex w-full items-center justify-between border border-white/10 bg-white/5 px-6 py-4"
                style={{ borderRadius: "0px" }}
              >
                <Skeleton className="h-5 w-48 bg-white/20 md:w-64" />
                <Skeleton className="h-4 w-4 bg-white/20" />
              </div>
            ))}
          </div>
        </div>
      </div>
    )
  }

  const pathwayOptions = nextSteps
    ? [
        { label: nextSteps.certificate_btn_text, href: ROUTES.CERTIFICATION },
        {
          label: nextSteps.learning_btn_text,
          href: ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE,
        },
        { label: nextSteps.advisory_btn_text, href: ROUTES.ADVISORY },
        { label: nextSteps.member_btn_text, href: ROUTES.MEMBERSHIP },
      ]
    : []

  return (
    <div className="w-full bg-background px-0 py-16">
      <div className="relative grid grid-cols-1 overflow-hidden rounded-[24px] border-3 border-[#F0D070] bg-primary shadow-lg lg:grid-cols-12">
        <div
          className="pointer-events-none absolute inset-y-0 right-0 z-0 w-1/2 opacity-40 blur-3xl"
          style={{
            background: `radial-gradient(130% 130% at 75% 50%, #CAA24A 0%, rgba(202, 162, 74, 0.15) 35%, transparent 80%)`,
          }}
        />

        {/* LEFT CONTENT BLOCK */}
        <div className="relative z-10 flex flex-col justify-center p-8 md:p-12 lg:col-span-6 lg:p-16">
          <span className="mb-4 text-[10px] font-bold tracking-[0.2em] text-muted-foreground uppercase opacity-90 md:text-xs">
            {nextSteps?.tagline || "Choose Your Next Step"}
          </span>

          <h2 className="text-3xl leading-tight font-medium tracking-tight text-white md:text-4xl lg:text-5xl">
            {nextSteps?.title1} <br className="hidden md:inline" />
            <span className="font-serif text-[#CAA24A] italic">
              {nextSteps?.title2}
            </span>
          </h2>
        </div>

        {/* RIGHT ACTIONABLE GRID ROWS */}
        <div className="relative z-10 flex flex-col justify-center space-y-3 border-white/10 bg-white/2 p-8 backdrop-blur-sm md:p-12 lg:col-span-6 lg:border-l lg:p-16">
          {pathwayOptions.map((option, index) => (
            <Link
              key={index}
              to={option.href}
              className="group flex w-full items-center justify-between border border-white/10 bg-white/5 px-6 py-4 text-white transition-all duration-200 hover:bg-[#CAA24A]"
              style={{
                borderRadius: "0px",
              }}
            >
              <span className="text-sm font-semibold tracking-wide md:text-base">
                {option.label}
              </span>

              <ArrowRight className="ml-4 h-4 w-4 shrink-0 transform transition-transform duration-200 group-hover:translate-x-1.5" />
            </Link>
          ))}
        </div>
      </div>
    </div>
  )
}
