import { useGetStrategicAdvisoryQuery } from "@/features/about/api/aboutApi";;
import { Skeleton } from "@/components/ui/skeleton";
import AutoIframe from "@/components/shared/AutoIframe";

function StrategicSkeleton() {
  return (
    <div className="bg-[#F7FAF9]">
      <section className="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
        <div
          className="rounded-3xl px-6 py-14 md:px-16"
          style={{
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
          }}
        >
          <div className="max-w-5xl">
            <Skeleton className="h-7 w-40 rounded-full bg-white/10" />
            <Skeleton className="mt-6 h-10 md:h-12 w-[60%] max-w-xl bg-white/10" />
            <div className="mt-6 max-w-5xl space-y-3">
              <Skeleton className="h-5 w-full bg-white/10" />
              <Skeleton className="h-5 w-full bg-white/10" />
              <Skeleton className="h-5 w-[85%] bg-white/10" />
            </div>
          </div>
        </div>
      </section>

      <section className="container mx-auto space-y-6 px-4 pb-8 pt-4 sm:px-6 lg:px-8">
        <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
          <Skeleton className="h-6 w-20 rounded-full bg-[#FBF4E2]" />
          <Skeleton className="mt-5 h-8 w-64 bg-gray-200" />
          <div className="mt-4 max-w-6xl space-y-2">
            <Skeleton className="h-4 w-full bg-gray-200" />
            <Skeleton className="h-4 w-[90%] bg-gray-200" />
            <Skeleton className="h-4 w-[85%] bg-gray-200" />
          </div>
          <Skeleton className="mt-7 h-6 w-32 bg-gray-200" />
          <div className="mt-4 space-y-3">
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <div key={i} className="flex gap-3">
                <Skeleton className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#D0A13A]" />
                <Skeleton className="h-4 w-3/4 bg-gray-200" />
              </div>
            ))}
          </div>
        </article>

        <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-6 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
          <Skeleton className="h-8 w-64 bg-gray-200" />
          <div className="mt-4 max-w-6xl space-y-2">
            <Skeleton className="h-4 w-full bg-gray-200" />
            <Skeleton className="h-4 w-[90%] bg-gray-200" />
          </div>
        </article>

        <div className="grid gap-6 lg:grid-cols-2">
          <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
            <Skeleton className="h-8 w-48 bg-gray-200" />
            <div className="mt-4 space-y-3">
              <Skeleton className="h-4 w-full bg-gray-200" />
              <Skeleton className="h-4 w-[80%] bg-gray-200" />
              <Skeleton className="h-4 w-[90%] bg-gray-200" />
            </div>
          </article>
          <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
            <Skeleton className="h-8 w-48 bg-gray-200" />
            <div className="mt-4 space-y-3">
              <Skeleton className="h-4 w-full bg-gray-200" />
              <Skeleton className="h-4 w-[85%] bg-gray-200" />
              <Skeleton className="h-4 w-[95%] bg-gray-200" />
            </div>
          </article>
        </div>

        <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
          <Skeleton className="h-8 w-56 bg-gray-200" />
          <div className="mt-4 space-y-3">
            <Skeleton className="h-4 w-full bg-gray-200" />
            <Skeleton className="h-4 w-[75%] bg-gray-200" />
            <Skeleton className="h-4 w-[85%] bg-gray-200" />
          </div>
        </article>
      </section>
    </div>
  )
}

export default function StrategicAdvisoryBoard() {
  const { data, isLoading, error } = useGetStrategicAdvisoryQuery();

  if (isLoading) {
    return <StrategicSkeleton />;
  }

  if (error || !data?.data?.strategic_advisories) {
    return (
      <main className="bg-[#F7FAF9] min-h-screen flex items-center justify-center">
        <p className="text-gray-500">Failed to load strategic advisory data.</p>
      </main>
    );
  }

  const saData = data.data.strategic_advisories;

  if (saData.injected_status && saData.content_file) {
    return (
      <main className="bg-white min-h-screen">
        <AutoIframe src={saData.content_file} />
      </main>
    );
  }

  const appointmentParagraphs = saData.appointment_short_description
    ? saData.appointment_short_description.split(/\r?\n\r?\n/).filter(Boolean)
    : [];

  const conflictParagraphs = saData.conflict_short_description
    ? saData.conflict_short_description.split(/\r?\n\r?\n/).filter(Boolean)
    : [];

  return (
    <main className="bg-[#F7FAF9]">
      <section className="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
        <div
          className="rounded-3xl px-6 py-14 md:px-16"
          style={{
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
          }}
        >
          <div className="max-w-5xl">
            <div className="w-fit rounded-full border border-[#D4AA3A]/70 bg-[#D4AA3A]/10 px-3.5 py-2 text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-[#D4AA3A]">
              {saData.tagline}
            </div>
            <h1 className="mt-6 font-serif text-4xl font-medium leading-tight text-white md:text-5xl">
              {saData.title1}{" "}
              <span className="italic text-[#D4AA3A]">{saData.title2}</span>
            </h1>
            <p className="mt-6 max-w-5xl text-sm leading-relaxed text-[#D8E6E1]">
              {saData.short_description}
            </p>
          </div>
        </div>
      </section>

      <section className="container mx-auto space-y-6 px-4 pb-8 pt-4 sm:px-6 lg:px-8">
        <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
          <div className="w-fit rounded-full bg-[#FBF4E2] px-3 py-1.5 text-[0.64rem] font-semibold uppercase tracking-[0.08em] text-[#C39A31]">
            {saData.purpose_tagline}
          </div>
          <h1 className="mt-5 text-3xl font-bold leading-tight text-[#101917] sm:text-[2.15rem]">
            {saData.purpose_title}
          </h1>
          <p className="mt-4 max-w-6xl text-sm leading-6 text-[#48655D]">
            {saData.purpose_short_description}
          </p>

          <h2 className="mt-7 text-lg font-bold uppercase tracking-[0.02em] text-[#D0A13A]">
            {saData.advisory_title}
          </h2>
          <ul className="mt-4 space-y-3 text-sm leading-5 text-[#48655D]">
            {saData.strategic_advisory_features.map((item) => (
              <li key={item.id} className="flex gap-3">
                <span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#D0A13A]" />
                <span>{item.description}</span>
              </li>
            ))}
          </ul>
        </article>

        <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-6 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
          <h2 className="text-2xl font-bold leading-tight text-[#D0A13A]">
            {saData.panel_title}
          </h2>
          <p className="mt-4 max-w-6xl text-sm leading-6 text-[#48655D]">
            {saData.panel_short_description}
          </p>
        </article>

        <div className="grid gap-6 lg:grid-cols-2">
          <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
            <h2 className="text-2xl font-bold leading-tight text-[#D0A13A]">
              {saData.appointment_title}
            </h2>
            <div className="mt-4 space-y-4 text-sm leading-6 text-[#48655D]">
              {appointmentParagraphs.map((p, index) => (
                <p key={index}>{p}</p>
              ))}
            </div>
          </article>

          <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
            <h2 className="text-2xl font-bold leading-tight text-[#D0A13A]">
              {saData.conflict_title}
            </h2>
            <div className="mt-4 space-y-4 text-sm leading-6 text-[#48655D]">
              {conflictParagraphs.map((p, index) => (
                <p key={index}>{p}</p>
              ))}
            </div>
          </article>
        </div>

        <article className="rounded-[18px] border border-[#DDE8E4] bg-white px-6 py-7 shadow-[0_10px_28px_rgba(15,47,38,0.03)] sm:px-7">
          <h2 className="text-2xl font-bold leading-tight text-[#D0A13A]">
            {saData.expression_title}
          </h2>
          <div 
            className="mt-4 text-sm leading-6 text-[#48655D] prose prose-sm max-w-none prose-p:my-2 prose-a:font-bold prose-a:text-[#102B24] hover:prose-a:underline"
            dangerouslySetInnerHTML={{ __html: saData.expression_description }} 
          />
        </article>
      </section>
    </main>
  );
}
