import { Button } from "@/components/ui/button"
import { useGetAdvisoryServicesQuery } from "@/features/advisory/api/advisoryApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Link } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

export default function AdvisoryHero() {
  const { data: response, isLoading } = useGetAdvisoryServicesQuery()
  const headers = response?.data?.advisory_headers
  const focuses = response?.data?.advisory_focuses

  if (isLoading) {
    return (
      <div className="w-full font-sans">
        <div
          style={{
            borderRadius: "24px",
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
          }}
          className="grid grid-cols-1 items-center gap-8 p-8 text-white shadow-lg md:p-12 lg:grid-cols-5"
        >
          <div className="space-y-6 lg:col-span-3">
            <Skeleton className="h-8 w-48 rounded-full bg-white/20" />

            <div className="space-y-3">
              <Skeleton className="h-10 w-full max-w-xl bg-white/20 md:h-12" />
              <Skeleton className="h-10 w-3/4 max-w-lg bg-white/20 md:h-12" />
            </div>

            <div className="space-y-2">
              <Skeleton className="h-4 w-full max-w-xl bg-white/20" />
              <Skeleton className="h-4 w-11/12 max-w-xl bg-white/20" />
              <Skeleton className="h-4 w-4/5 max-w-xl bg-white/20" />
            </div>

            <div className="pt-2">
              <Skeleton className="h-11 w-56 rounded-full bg-[#F0D070]/60" />
            </div>
          </div>

          <div className="space-y-6 rounded-2xl border border-white/10 bg-white p-6 shadow-xl md:p-8 lg:col-span-2">
            <div className="space-y-3">
              <Skeleton className="h-8 w-48 bg-neutral-200" />
              <div className="space-y-2">
                <Skeleton className="h-3 w-full bg-neutral-100" />
                <Skeleton className="h-3 w-5/6 bg-neutral-100" />
              </div>
            </div>

            <div className="space-y-3">
              {Array.from({ length: 3 }).map((_, idx) => (
                <div
                  key={idx}
                  className="flex items-start gap-3 rounded-xl border border-neutral-100 bg-[#f8faf9] p-3"
                >
                  <Skeleton className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-neutral-300" />
                  <div className="w-full space-y-1.5">
                    <Skeleton className="h-3 w-full bg-neutral-200" />
                    <Skeleton className="h-3 w-4/5 bg-neutral-200" />
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="w-full font-sans">
      <div
        style={{
          borderRadius: "24px",
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
        }}
        className="grid grid-cols-1 items-center gap-8 p-8 text-white shadow-lg md:p-12 lg:grid-cols-5"
      >
        <div className="space-y-6 lg:col-span-3">
          <div className="inline-block rounded-full border border-white/5 bg-white/10 px-4 py-1.5 backdrop-blur-sm">
            <span className="text-[10px] font-semibold tracking-widest text-yellow-500/90 uppercase md:text-xs">
              {headers?.tagline}
            </span>
          </div>

          <h1 className="font-serif text-3xl leading-tight font-normal tracking-tight md:text-5xl">
            {headers?.title1}{" "}
            <span className="font-serif font-normal text-yellow-400 italic">
              {headers?.title2}
            </span>
          </h1>

          <p className="max-w-xl font-sans text-xs leading-relaxed font-light text-neutral-300 md:text-sm">
            {headers?.description}
          </p>

          <div className="pt-2">
            <Button
              asChild
              className="h-11 rounded-full border border-transparent bg-[#F0D070] px-6 text-xs font-bold tracking-wide text-black shadow-none transition-colors hover:bg-[#F0D070]/90"
            >
              <Link to={ROUTES.REQUEST_ADVISORY_CONSULTATION}>
                Request Advisory Services
              </Link>
            </Button>
          </div>
        </div>

        {/* Right Grid Layout: Focused AI Panel Cards */}
        <div className="space-y-6 rounded-2xl border border-white/10 bg-white p-6 text-neutral-900 shadow-xl md:p-8 lg:col-span-2">
          <div className="space-y-2">
            <h2 className="font-serif text-xl font-semibold tracking-tight text-[#0F2F26] md:text-2xl">
              {focuses?.title}
            </h2>
            <p className="text-xs leading-relaxed font-light text-neutral-500">
              {focuses?.description}
            </p>
          </div>

          {/* List items block */}
          <div className="space-y-3">
            {focuses?.advisory_focus_features?.map((feature) => (
              <div
                key={feature.id}
                className="flex items-start gap-3 rounded-xl border border-neutral-100 bg-[#f8faf9] p-3"
              >
                <span className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#A57C1B]" />
                <p className="text-xs leading-normal font-medium text-neutral-700">
                  {feature.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
