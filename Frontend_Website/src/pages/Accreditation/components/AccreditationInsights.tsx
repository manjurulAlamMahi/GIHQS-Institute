import SectionTitle from "./SectionTitle";
import { useGetAccreditationDetailsQuery } from "@/features/accreditation/api/accreditationApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function AccreditationInsights() {
  const { data: response, isLoading } = useGetAccreditationDetailsQuery();
  const insightData = response?.data?.accreditation_insights;

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 pb-20 pt-6 sm:px-6 lg:px-8">
        <div className="space-y-10">
          <div className="flex justify-center">
            <Skeleton className="h-6 w-32 rounded-full bg-neutral-200" />
          </div>

          <div className="mx-auto max-w-3xl space-y-3 flex flex-col items-center">
            <Skeleton className="h-10 w-80 bg-neutral-200" />
            <Skeleton className="h-4 w-3/4 bg-neutral-100 mt-2" />
          </div>

          <div className="rounded-3xl bg-[linear-gradient(180deg,rgba(199,164,77,0.14)_0%,rgba(24,71,58,0.14)_94.42%,rgba(232,215,165,0.14)_188.85%)] p-6 md:p-10">
            <div className="grid gap-6 lg:grid-cols-2">
              <article className="rounded-xl border border-white/8 bg-[linear-gradient(123deg,#0C2A1F_0%,#0F3828_40%,#1A5C4A_100%)] p-7 shadow-[0_16px_36px_rgba(15,47,38,0.14)]">
                <Skeleton className="h-3 w-16 bg-[#D4AA3A]/40" />
                <Skeleton className="mt-4 h-6 w-3/4 bg-white/20" />
                <div className="mt-4 space-y-2">
                  <Skeleton className="h-4 w-full bg-white/10" />
                  <Skeleton className="h-4 w-full bg-white/10" />
                  <Skeleton className="h-4 w-5/6 bg-white/10" />
                </div>
              </article>

              <article className="rounded-lg border-l-3 border-[#D4AA3A] bg-white p-7 shadow-[0_16px_36px_rgba(15,47,38,0.08)]">
                <Skeleton className="h-3 w-16 bg-[#C39A31]/40" />
                <Skeleton className="mt-4 h-6 w-3/4 bg-neutral-200" />
                <div className="mt-4 space-y-2">
                  <Skeleton className="h-4 w-full bg-neutral-100" />
                  <Skeleton className="h-4 w-full bg-neutral-100" />
                  <Skeleton className="h-4 w-5/6 bg-neutral-100" />
                </div>
              </article>
            </div>

            <div className="mt-6 grid gap-4 md:grid-cols-3">
              {Array.from({ length: 3 }).map((_, i) => (
                <article
                  key={i}
                  className="rounded-md border border-[#D4AA3A] bg-[#F7FAF9] p-5 space-y-3"
                >
                  <Skeleton className="h-5 w-48 bg-neutral-200" />
                  <div className="space-y-2 pt-1">
                    <Skeleton className="h-4 w-full bg-neutral-100" />
                    <Skeleton className="h-4 w-3/4 bg-neutral-100" />
                  </div>
                </article>
              ))}
            </div>
          </div>
        </div>
      </section>
    );
  }

  // The first two items are top featured insights, the rest are regular cards
  const features = insightData?.accreditation_insights_features || [];
  const topInsight1 = features[0];
  const topInsight2 = features[1];
  const otherInsights = features.slice(2);

  return (
    <section className="container mx-auto px-4 pb-20 pt-6 sm:px-6 lg:px-8">
      <div className="space-y-10">
        <SectionTitle label="Insights" />

        <div className="mx-auto max-w-3xl space-y-3 text-center">
          <h2 className="text-3xl font-medium leading-tight text-[#0F2F26] md:text-4xl">
            {insightData?.title1}{" "}
            <span className="font-serif italic text-[#D4AA3A]">{insightData?.title2}</span>
          </h2>

          <p className="text-sm leading-relaxed text-[#4F6A61]">
            {insightData?.description}
          </p>
        </div>

        <div className="rounded-3xl bg-[linear-gradient(180deg,rgba(199,164,77,0.14)_0%,rgba(24,71,58,0.14)_94.42%,rgba(232,215,165,0.14)_188.85%)] p-6 md:p-10">
          <div className="grid gap-6 lg:grid-cols-2">
            {topInsight1 && (
              <article className="rounded-xl border border-white/8 bg-[linear-gradient(123deg,#0C2A1F_0%,#0F3828_40%,#1A5C4A_100%)] p-7 text-white shadow-[0_16px_36px_rgba(15,47,38,0.14)]">
                <p className="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-[#D4AA3A]">
                  {topInsight1.tagline || 'Insight'}
                </p>
                <h3 className="mt-4 text-lg font-bold">
                  {topInsight1.title}
                </h3>
                <p className="mt-4 text-sm leading-relaxed text-[#D8E6E1]">
                  {topInsight1.description}
                </p>
              </article>
            )}

            {topInsight2 && (
              <article className="rounded-lg border-l-3 border-[#D4AA3A] bg-white p-7 shadow-[0_16px_36px_rgba(15,47,38,0.08)]">
                <p className="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-[#C39A31]">
                  {topInsight2.tagline || 'Insight'}
                </p>
                <h3 className="mt-4 text-lg font-bold text-[#102D25]">
                  {topInsight2.title}
                </h3>
                <p className="mt-4 text-sm leading-relaxed text-[#4F6A61]">
                  {topInsight2.description}
                </p>
              </article>
            )}
          </div>

          <div className="mt-6 grid gap-4 md:grid-cols-3">
            {otherInsights.map((card) => (
              <article
                key={card.id}
                className="rounded-md border border-[#D4AA3A] bg-[#F7FAF9] p-5"
              >
                <h4 className="text-sm font-bold text-[#102D25]">
                  {card.title}
                </h4>
                <p className="mt-3 text-sm leading-relaxed text-[#4F6A61]">
                  {card.description}
                </p>
              </article>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
