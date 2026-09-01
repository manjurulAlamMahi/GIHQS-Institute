import { Skeleton } from "@/components/ui/skeleton"

export function UniversalPageSkeleton() {
  return (
    <div className="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
      <div
        className="grid overflow-hidden rounded-3xl px-6 py-10 shadow-[0_22px_60px_rgba(15,47,38,0.16)] md:grid-cols-[1fr_0.95fr] md:gap-10 md:px-12 md:py-14 lg:px-16"
        style={{
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
        }}
      >
        <div className="flex flex-col justify-center">
          <Skeleton className="h-7 w-40 rounded-full bg-white/10" />

          <div className="mt-6 space-y-3">
            <Skeleton className="h-10 w-full max-w-xl bg-white/10 md:h-12" />
            <Skeleton className="h-10 w-3/4 max-w-xl bg-white/10 md:h-12" />
          </div>

          <div className="mt-7 max-w-xl space-y-5">
            {[0, 1, 2, 3].map((item) => (
              <div key={item} className="space-y-2">
                <Skeleton className="h-4 w-full bg-white/10" />
                <Skeleton className="h-4 w-[92%] bg-white/10" />
                {item % 2 === 0 && <Skeleton className="h-4 w-[80%] bg-white/10" />}
              </div>
            ))}
          </div>
        </div>

        <div className="mt-10 flex items-center justify-center md:mt-0">
          <div className="aspect-square w-full max-w-md rounded-[6rem] border-10 border-white/5 bg-transparent p-0">
            <Skeleton className="h-full w-full rounded-[5.5rem] bg-white/10" />
          </div>
        </div>
      </div>

      <div className="pt-8 pb-16">
        <div className="flex flex-col gap-5">
          {[1, 2, 3, 4, 5, 6].map((item) => (
            <div
              key={item}
              className="flex min-h-26 items-center justify-between rounded-lg border border-[#D9E5E1] bg-white px-6 shadow-[0_8px_24px_rgba(15,47,38,0.04)]"
            >
              <Skeleton className="h-6 w-1/2 bg-[#0F4A3B]/10" />
              <Skeleton className="h-8 w-8 shrink-0 rounded-full bg-[#0F4A3B]/10" />
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}
