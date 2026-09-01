import { useGetVisionMissionValuesQuery } from "@/features/about/api/aboutApi";;
import { Skeleton } from "@/components/ui/skeleton";
import AutoIframe from "@/components/shared/AutoIframe";

function MissionSkeleton() {
  return (
    <div className="bg-[#F7FAF9] py-6 px-4 md:px-8 mx-auto container space-y-10">
      <section>
        <div
          className="rounded-[22px] px-6 py-10 sm:px-10 lg:px-14"
          style={{
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0B3027 0%, #173C2B 58%, #0D251C 100%)",
          }}
        >
          <div className="max-w-5xl">
            <Skeleton className="h-7 w-40 rounded-full bg-white/10" />
            <Skeleton className="mt-7 h-10 md:h-12 w-[80%] max-w-xl bg-white/10" />
            <div className="mt-6 max-w-4xl space-y-3">
              <Skeleton className="h-5 w-full bg-white/10" />
              <Skeleton className="h-5 w-full bg-white/10" />
              <Skeleton className="h-5 w-[85%] bg-white/10" />
            </div>
          </div>
        </div>
      </section>

      <div className="grid gap-6 lg:grid-cols-2">
        {[1, 2].map((i) => (
          <div key={i} className="rounded-[20px] border border-[#DFE8E4] bg-white px-6 py-7 shadow-[0_16px_36px_rgba(15,47,38,0.04)] sm:px-8">
            <Skeleton className="h-6 w-24 rounded-full bg-[#C49B2E]/20" />
            <Skeleton className="mt-5 h-8 w-3/4 bg-gray-200" />
            <Skeleton className="mt-5 h-5 w-full bg-gray-200" />
            <Skeleton className="mt-2 h-5 w-[90%] bg-gray-200" />
          </div>
        ))}
      </div>

      <div className="rounded-[22px] bg-[#0F2F26] px-5 py-8 sm:px-8 lg:px-10">
        <div className="max-w-5xl">
          <Skeleton className="h-6 w-32 rounded-full bg-white/10" />
          <Skeleton className="mt-5 h-8 md:h-10 w-[60%] max-w-md bg-white/10" />
          <div className="mt-3 max-w-5xl space-y-2">
            <Skeleton className="h-4 w-full bg-white/10" />
            <Skeleton className="h-4 w-[90%] bg-white/10" />
          </div>
        </div>

        <div className="mt-7 grid gap-6 lg:grid-cols-6">
          {[1, 2, 3, 4, 5].map((i, index) => (
            <div
              key={i}
              className={`rounded-[16px] bg-white p-6 shadow-[0_12px_30px_rgba(2,16,12,0.12)] ${
                index < 3 ? "lg:col-span-2" : "lg:col-span-3"
              }`}
            >
              <Skeleton className="h-12 w-12 rounded-[14px] bg-[#F1EAD5]" />
              <Skeleton className="mt-5 h-6 w-40 bg-gray-200" />
              <div className="mt-3 space-y-2">
                <Skeleton className="h-4 w-full bg-gray-200" />
                <Skeleton className="h-4 w-full bg-gray-200" />
                <Skeleton className="h-4 w-[80%] bg-gray-200" />
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default function MissionVisionValues() {
  const { data, isLoading, error } = useGetVisionMissionValuesQuery();

  if (isLoading) {
    return <MissionSkeleton />;
  }

  if (error || !data?.data?.vision_mission_values) {
    return (
      <main className="bg-[#F7FAF9] min-h-screen flex items-center justify-center">
        <p className="text-gray-500">Failed to load mission and vision data.</p>
      </main>
    );
  }

  const vmvData = data.data.vision_mission_values;

  if (vmvData.injected_status && vmvData.content_file) {
    return (
      <main className="bg-white min-h-screen">
        <AutoIframe src={vmvData.content_file} />
      </main>
    );
  }

  const overviewCards = [
    {
      eyebrow: vmvData.vision_tagline,
      title: vmvData.vision_title,
      description: vmvData.vision_short_description,
    },
    {
      eyebrow: vmvData.mission_tagline,
      title: vmvData.mission_title,
      description: vmvData.mission_short_description,
    },
  ];

  const coreValues = [
    {
      letter: vmvData.global_perspective_tagline,
      title: vmvData.global_perspective_title,
      description: vmvData.global_perspective_short_description,
    },
    {
      letter: vmvData.integrity_tagline,
      title: vmvData.integrity_title,
      description: vmvData.integrity_short_description,
    },
    {
      letter: vmvData.human_centered_tagline,
      title: vmvData.human_centered_title,
      description: vmvData.human_centered_short_description,
    },
    {
      letter: vmvData.quality_excellence_tagline,
      title: vmvData.quality_excellence_title,
      description: vmvData.quality_excellence_short_description,
    },
    {
      letter: vmvData.safety_leadership_tagline,
      title: vmvData.safety_leadership_title,
      description: vmvData.safety_leadership_short_description,
    },
  ];

  return (
    <main className="bg-[#F7FAF9] py-6 px-4 md:px-8 mx-auto container space-y-10">
      <section>
        <div
          className="rounded-[22px] px-6 py-10 sm:px-10 lg:px-14"
          style={{
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0B3027 0%, #173C2B 58%, #0D251C 100%)",
          }}
        >
          <div className="max-w-5xl">
            <div className="w-fit rounded-full border border-[#CDAA47]/70 px-4 py-1.5 text-[0.68rem] font-medium uppercase tracking-[0.14em] text-[#D7B853]">
              {vmvData.tagline}
            </div>
            <h1 className="mt-7 font-serif text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-[2.65rem]">
              {vmvData.title1}{" "}
              <span className="italic text-[#D7B853]">{vmvData.title2}</span>
            </h1>
            <p className="mt-6 max-w-4xl text-sm leading-7 text-[#DDEAE5]">
              {vmvData.short_description}
            </p>
          </div>
        </div>
      </section>

      <div className="grid gap-6 lg:grid-cols-2">
        {overviewCards.map((item) => (
          <article
            key={item.eyebrow}
            className="rounded-[20px] border border-[#DFE8E4] bg-white px-6 py-7 shadow-[0_16px_36px_rgba(15,47,38,0.04)] sm:px-8"
          >
            <div className="w-fit rounded-full border border-[#D6B767]/70 bg-[#FFF9E8] px-3 py-1.5 text-[0.63rem] font-semibold uppercase tracking-[0.12em] text-[#C49B2E]">
              {item.eyebrow}
            </div>
            <h2 className="mt-5 max-w-2xl font-serif text-xl font-semibold leading-snug text-[#112C24] sm:text-2xl lg:text-[1.55rem]">
              {item.title}
            </h2>
            <p className="mt-5 max-w-2xl text-sm leading-7 text-[#61756D]">
              {item.description}
            </p>
          </article>
        ))}
      </div>

      <div className="rounded-[22px] bg-[#0F2F26] px-5 py-8 sm:px-8 lg:px-10">
        <div className="max-w-5xl">
          <div className="w-fit rounded-full border border-[#CDAA47]/70 px-3 py-1.5 text-[0.62rem] font-semibold uppercase tracking-[0.13em] text-[#D7B853]">
            {vmvData.value_tagline}
          </div>
          <h2 className="mt-5 font-serif text-2xl font-semibold leading-tight text-white sm:text-3xl">
            {vmvData.value_title}{" "}
            <span className="italic text-[#D7B853]">{vmvData.value_title2}</span>
          </h2>
          <p className="mt-3 max-w-5xl text-sm leading-6 text-[#DDEAE5]">
            {vmvData.value_short_description}
          </p>
        </div>

        <div className="mt-7 grid gap-6 lg:grid-cols-6">
          {coreValues.map((value, index) => (
            <article
              key={value.title}
              className={`rounded-[16px] bg-white p-6 shadow-[0_12px_30px_rgba(2,16,12,0.12)] ${
                index < 3 ? "lg:col-span-2" : "lg:col-span-3"
              }`}
            >
              <div className="flex h-12 w-12 items-center justify-center rounded-[14px] bg-[#F1EAD5] text-xl font-bold text-[#1A3A31]">
                {value.letter}
              </div>
              <h3 className="mt-5 text-base font-bold text-[#112C24]">
                {value.title}
              </h3>
              <p className="mt-3 text-sm leading-6 text-[#62766E]">
                {value.description}
              </p>
            </article>
          ))}
        </div>
      </div>
    </main>
  );
}
