import { Button } from "@/components/ui/button"
import { Skeleton } from "@/components/ui/skeleton"
import { useGetAccreditationHeaderQuery } from "@/features/accreditation/api/accreditationApi"
import { Check } from "lucide-react"
import { Link } from "react-router"
export default function AccreditationHero() {
  const { data: response, isLoading } = useGetAccreditationHeaderQuery()
  const header = response?.data?.accreditation_headers

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
        <div className="relative my-2 flex min-h-144 flex-col gap-10 overflow-hidden rounded-3xl bg-[#0F2F26] px-6 py-12 md:my-6 md:min-h-140 md:flex-row md:items-center md:justify-between md:px-12 lg:px-20">
          <div className="relative z-10 w-full space-y-7 md:max-w-2xl">
            <Skeleton className="h-8 w-32 rounded-full bg-white/20" />
            <div className="space-y-4">
              <Skeleton className="h-10 w-64 bg-white/20 md:h-12" />
              <div className="space-y-2">
                <Skeleton className="h-5 w-full bg-white/20" />
                <Skeleton className="h-5 w-3/4 bg-white/20" />
              </div>
            </div>
            <div className="max-w-2xl rounded-lg border border-white/10 bg-white/10 px-5 py-4">
              <Skeleton className="h-4 w-full bg-white/20" />
              <Skeleton className="mt-2 h-4 w-2/3 bg-white/20" />
            </div>
            <div className="flex flex-col gap-3 sm:flex-row">
              <Skeleton className="h-12 w-48 rounded-full bg-white/20" />
              <Skeleton className="h-12 w-64 rounded-full bg-white/20" />
            </div>
            <div className="flex max-w-xl flex-wrap gap-3">
              {Array.from({ length: 4 }).map((_, i) => (
                <Skeleton
                  key={i}
                  className="h-8 w-32 rounded-full bg-white/20"
                />
              ))}
            </div>
          </div>
          <aside className="relative z-10 w-full rounded-xl border border-white/12 bg-white/10 p-6 backdrop-blur md:max-w-md">
            <Skeleton className="mb-5 h-4 w-24 bg-white/20" />
            <div className="space-y-3">
              {Array.from({ length: 4 }).map((_, i) => (
                <div
                  key={i}
                  className="flex gap-3 rounded-lg border border-white/8 bg-white/8 p-4"
                >
                  <Skeleton className="h-7 w-7 shrink-0 rounded-full bg-white/20" />
                  <div className="w-full space-y-2">
                    <Skeleton className="h-4 w-3/4 bg-white/20" />
                    <Skeleton className="h-3 w-5/6 bg-white/20" />
                  </div>
                </div>
              ))}
            </div>
            <Skeleton className="mt-5 h-4 w-full bg-white/20" />
          </aside>
        </div>
      </section>
    )
  }

  return (
    <section className="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
      <div className="relative my-2 flex min-h-144 flex-col gap-10 overflow-hidden rounded-3xl bg-[#0F2F26] px-6 py-12 md:my-6 md:min-h-140 md:flex-row md:items-center md:justify-between md:px-12 lg:px-20">
        <div
          className="pointer-events-none absolute inset-y-0 right-0 w-2/3 opacity-85 blur-3xl"
          style={{
            background:
              "radial-gradient(140% 140% at 72% 50%, rgba(212, 170, 58, 0.22) 0%, rgba(212, 170, 58, 0.12) 24%, rgba(212, 170, 58, 0.06) 48%, rgba(15, 47, 38, 0) 80%)",
          }}
        />

        <div className="relative z-10 w-full space-y-7 md:max-w-2xl">
          <div className="inline-flex rounded-full border border-[rgba(240,208,112,0.72)] bg-[rgba(240,208,112,0.10)] px-3.5 py-2 text-[0.68rem] leading-none font-semibold tracking-wider text-[#D4AA3A] uppercase">
            {header?.tagline}
          </div>

          <div className="space-y-4">
            <h1 className="text-4xl leading-tight font-medium text-white md:text-5xl">
              {header?.title1}{" "}
              <span className="font-serif text-[#D4AA3A] italic">
                {header?.title2}
              </span>
            </h1>
            <p className="max-w-2xl text-base leading-relaxed text-[#B8C5C0] md:text-lg">
              {header?.description}
            </p>
          </div>

          <div className="max-w-2xl rounded-lg border border-white/10 bg-white/7 px-5 py-4 shadow-[inset_2px_0_0_#D4AA3A]">
            <p className="text-sm leading-relaxed text-[#B8C5C0]">
              {header?.note}
            </p>
          </div>

          <div className="flex flex-col gap-3 sm:flex-row">
            <Button
              asChild
              className="h-12 rounded-full bg-[#F4C84E] px-7 text-sm font-bold text-[#102D25] hover:bg-[#EABF45]"
            >
              <Link to="/accreditation/apply">{header?.apply_btn_text}</Link>
            </Button>
            <Button
              asChild
              variant="outline"
              className="h-12 rounded-full border-white/25 bg-transparent px-7 text-sm font-semibold text-white hover:bg-white/10 hover:text-white"
            >
              <Link to="/accreditation/standard-manual">
                {header?.download_btn_text}
              </Link>
            </Button>
          </div>

          <div className="flex max-w-xl flex-wrap gap-3">
            {header?.accreditation_tags?.map((tag) => (
              <span
                key={tag.id}
                className="rounded-full border border-white/15 px-4 py-2 text-xs font-medium text-[#9FB3AD]"
              >
                {tag.tagname}
              </span>
            ))}
          </div>
        </div>

        <aside className="relative z-10 w-full rounded-xl border border-white/12 bg-white/10 p-6 shadow-[0_24px_60px_rgba(0,0,0,0.18)] backdrop-blur md:max-w-md">
          <p className="mb-5 text-[0.7rem] font-bold tracking-[0.22em] text-[#9FB3AD] uppercase">
            Key Facts
          </p>

          <div className="space-y-3">
            {header?.accreditation_keyfacts?.map((item) => (
              <div
                key={item.id}
                className="flex gap-3 rounded-lg border border-white/8 bg-white/8 p-4"
              >
                <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#D4AA3A] text-[#12352B]">
                  <Check className="h-4 w-4 stroke-3" />
                </span>
                <div className="min-w-0">
                  <h2 className="text-sm font-bold text-white">{item.title}</h2>
                  <p className="mt-1 text-xs leading-relaxed text-[#A9B8B3]">
                    {item.subtitle}
                  </p>
                </div>
              </div>
            ))}
          </div>

          <p className="mt-5 text-xs leading-relaxed text-[#9FB3AD]">
            Designed for programs and credentials in healthcare quality and
            patient safety.
          </p>
        </aside>
      </div>
    </section>
  )
}
