import { Diamond } from "lucide-react"
import { useGetAccreditationApplyHeroQuery } from "@/features/accreditation/api/accreditationApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function ApplyAccreditationHero() {
  const { data: response, isLoading } = useGetAccreditationApplyHeroQuery();
  const heroData = response?.data?.accreditation_apply_hero;
  const snapshotData = response?.data?.accreditation_eligibility_snapshot;

  if (isLoading) {
    return (
      <section className="grid gap-6 lg:grid-cols-[1.15fr_0.95fr]">
        <div
          style={{
            background:
              "linear-gradient(180deg, rgba(199, 164, 77, 0.14) 0%, rgba(24, 71, 58, 0.14) 94.42%, rgba(232, 215, 165, 0.14) 188.85%)",
          }}
          className="rounded-[26px] p-8 shadow-[0_18px_42px_rgba(15,47,38,0.08)] md:p-10"
        >
          <Skeleton className="h-8 w-32 rounded-full bg-[#0F2F26]/10" />
          
          <div className="mt-7 space-y-3">
            <Skeleton className="h-10 w-3/4 max-w-2xl bg-[#0F2F26]/10" />
            <Skeleton className="h-10 w-1/2 max-w-2xl bg-[#0F2F26]/10" />
          </div>

          <div className="mt-5 space-y-2">
            <Skeleton className="h-4 w-full max-w-2xl bg-[#183B33]/10" />
            <Skeleton className="h-4 w-full max-w-2xl bg-[#183B33]/10" />
            <Skeleton className="h-4 w-3/4 max-w-2xl bg-[#183B33]/10" />
          </div>

          <div className="mt-7 flex gap-4 rounded-2xl bg-white/70 p-5 shadow-[0_18px_42px_rgba(15,47,38,0.07)] backdrop-blur">
            <Skeleton className="h-8 w-24 shrink-0 rounded-full bg-[#0F2F26]/10" />
            <div className="space-y-2 w-full">
              <Skeleton className="h-4 w-full bg-[#5d756f]/10" />
              <Skeleton className="h-4 w-5/6 bg-[#5d756f]/10" />
            </div>
          </div>
        </div>

        <div className="relative overflow-hidden rounded-2xl bg-white p-4 before:absolute before:inset-x-0 before:bottom-0 before:h-1/2 before:bg-[#153E32]">
          <aside
            style={{
              borderRadius: "18px",
              border: "1px solid rgba(255, 255, 255, 0.08)",
              background:
                "linear-gradient(137deg, rgba(15, 47, 38, 0.98) 0%, rgba(24, 71, 58, 0.96) 100%)",
              boxShadow: "0 18px 36px 0 rgba(8, 30, 23, 0.16)",
            }}
            className="relative overflow-hidden p-8 text-white md:p-10"
          >
            <div className="relative z-10">
              <Skeleton className="h-8 w-48 bg-white/20" />
              <div className="mt-5 space-y-2">
                <Skeleton className="h-4 w-full bg-[#e5efec]/20" />
                <Skeleton className="h-4 w-full bg-[#e5efec]/20" />
                <Skeleton className="h-4 w-3/4 bg-[#e5efec]/20" />
              </div>
              
              <div className="mt-7 space-y-3">
                {Array.from({ length: 3 }).map((_, i) => (
                  <div
                    key={i}
                    className="flex gap-3 rounded-xl border border-white/10 bg-white/8 p-4"
                  >
                    <Skeleton className="mt-1 h-3 w-3 shrink-0 rounded-full bg-[#F0D070]/40" />
                    <div className="space-y-2 w-full">
                      <Skeleton className="h-4 w-1/3 bg-[#F0D070]/40" />
                      <Skeleton className="h-4 w-full bg-[#e9f2ef]/20" />
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </aside>
        </div>
      </section>
    );
  }

  return (
    <section className="grid gap-6 lg:grid-cols-[1.15fr_0.95fr]">
      <div
        style={{
          background:
            "linear-gradient(180deg, rgba(199, 164, 77, 0.14) 0%, rgba(24, 71, 58, 0.14) 94.42%, rgba(232, 215, 165, 0.14) 188.85%)",
        }}
        className="rounded-[26px] p-8 shadow-[0_18px_42px_rgba(15,47,38,0.08)] md:p-10"
      >
        <span className="inline-flex rounded-full bg-[#f2ead4] px-5 py-2 text-xs font-bold tracking-[0.16em] text-[#0F2F26] uppercase">
          {heroData?.tagline}
        </span>
        <h1 className="mt-7 max-w-2xl font-serif text-4xl leading-tight font-semibold text-[#0F2F26] md:text-5xl">
          {heroData?.title1} {heroData?.title2}
        </h1>
        <p className="mt-5 max-w-2xl text-base leading-8 text-[#183B33]">
          {heroData?.description}
        </p>
        <div className="mt-7 flex gap-4 rounded-2xl bg-white/70 p-5 shadow-[0_18px_42px_rgba(15,47,38,0.07)] backdrop-blur">
          <span className="h-fit rounded-full border border-[#d7e1de] bg-[#f8faf9] px-4 py-2 text-xs font-bold tracking-[0.16em] text-[#0F2F26] uppercase">
            Important
          </span>
          <p className="text-sm leading-7 text-[#5d756f]">
            {heroData?.note}
          </p>
        </div>
      </div>

      <div className="relative overflow-hidden rounded-2xl bg-white p-4 before:absolute before:inset-x-0 before:bottom-0 before:h-1/2 before:bg-[#153E32]">
        <aside
          style={{
            borderRadius: "18px",
            border: "1px solid rgba(255, 255, 255, 0.08)",
            background:
              "linear-gradient(137deg, rgba(15, 47, 38, 0.98) 0%, rgba(24, 71, 58, 0.96) 100%)",
            boxShadow: "0 18px 36px 0 rgba(8, 30, 23, 0.16)",
          }}
          className="relative overflow-hidden p-8 text-white md:p-10"
        >
          <div className="relative z-10">
            <h2 className="font-serif text-2xl font-medium">
              {snapshotData?.title}
            </h2>
            <p className="mt-5 text-base leading-7 text-[#e5efec]">
              {snapshotData?.description}
            </p>
            <div className="mt-7 space-y-3">
              {snapshotData?.accreditation_eligibility_snapshot_features?.map((item) => (
                <div
                  key={item.id}
                  className="flex gap-3 rounded-xl border border-white/10 bg-white/8 p-4"
                >
                  <Diamond className="mt-1 h-3 w-3 shrink-0 fill-white text-white" />
                  <p className="text-sm leading-5 text-[#e9f2ef]">
                    <span className="font-bold text-[#F0D070]">
                      {item.keypoints}:
                    </span>{" "}
                    {item.details}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </aside>
      </div>
    </section>
  )
}
