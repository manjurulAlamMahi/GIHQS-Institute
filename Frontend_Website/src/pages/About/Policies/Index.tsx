import { useGetPoliciesGovernanceQuery } from "@/features/about/api/aboutApi";;
import { Skeleton } from "@/components/ui/skeleton";
import AutoIframe from "@/components/shared/AutoIframe";

function PoliciesSkeleton() {
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
            <Skeleton className="h-7 w-56 rounded-full bg-white/10" />
            <Skeleton className="mt-6 h-10 md:h-12 w-[60%] max-w-xl bg-white/10" />
            <div className="mt-6 max-w-5xl space-y-3">
              <Skeleton className="h-5 w-full bg-white/10" />
              <Skeleton className="h-5 w-[90%] bg-white/10" />
              <Skeleton className="h-5 w-[95%] bg-white/10" />
            </div>
          </div>
        </div>
      </section>

      <section className="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div className="grid gap-6 lg:grid-cols-3">
          {[1, 2, 3].map((i) => (
            <div
              key={i}
              className="rounded-2xl border border-[#DDE8E4] bg-white p-7 shadow-[0_12px_32px_rgba(15,47,38,0.05)]"
            >
              <Skeleton className="h-11 w-11 rounded-xl bg-[#FBF4E2]" />
              <Skeleton className="mt-7 h-8 w-48 bg-gray-200" />
              <div className="mt-3 space-y-2">
                <Skeleton className="h-4 w-full bg-gray-200" />
                <Skeleton className="h-4 w-[85%] bg-gray-200" />
              </div>
              <div className="mt-6 space-y-3">
                {[1, 2, 3, 4].map((j) => (
                  <Skeleton key={j} className="h-12 w-full rounded-lg bg-[#EEF6F3]" />
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="container mx-auto px-4 pb-16 pt-2 sm:px-6 lg:px-8">
        <div
          className="rounded-3xl px-6 py-12 md:px-16"
          style={{
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
          }}
        >
          <Skeleton className="h-10 w-64 bg-white/10" />
          <div className="mt-6 max-w-5xl space-y-2">
            <Skeleton className="h-5 w-full bg-white/10" />
            <Skeleton className="h-5 w-[85%] bg-white/10" />
          </div>
        </div>
      </section>
    </div>
  )
}

export default function PoliciesGovernance() {
  const { data, isLoading, error } = useGetPoliciesGovernanceQuery();

  if (isLoading) {
    return <PoliciesSkeleton />;
  }

  if (error || !data?.data?.policies_governances) {
    return (
      <main className="bg-[#F7FAF9] min-h-screen flex items-center justify-center">
        <p className="text-gray-500">Failed to load policies data.</p>
      </main>
    );
  }

  const pgData = data.data.policies_governances;

  if (pgData.injected_status && pgData.content_file) {
    return (
      <main className="bg-white min-h-screen">
        <AutoIframe src={pgData.content_file} />
      </main>
    );
  }

  // Split description string into paragraphs safely
  const descriptionParagraphs = pgData.description
    ? pgData.description.split(/\r?\n\r?\n/).filter(Boolean)
    : [];

  const policyGroups = [
    {
      code: pgData.inst_tag,
      title: pgData.inst_title,
      description: pgData.inst_description,
      documents: pgData.institutional_documents,
      actions: true,
    },
    {
      code: pgData.cert_tag,
      title: pgData.cert_title,
      description: pgData.cert_description,
      documents: pgData.certification_documents,
    },
    {
      code: pgData.acc_tag,
      title: pgData.acc_title,
      description: pgData.acc_description,
      documents: pgData.accreditation_documents,
    },
  ];

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
              {pgData.tagline}
            </div>
            <h1 className="mt-6 font-serif text-4xl font-medium leading-tight text-white md:text-5xl">
              {pgData.title1}{" "}
              <span className="italic text-[#D4AA3A]">{pgData.title2}</span>
            </h1>
            <div className="mt-6 max-w-5xl space-y-5 text-sm leading-relaxed text-[#D8E6E1]">
              {descriptionParagraphs.map((paragraph, index) => (
                <p key={index}>{paragraph}</p>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div className="grid gap-6 lg:grid-cols-3">
          {policyGroups.map((group) => (
            <article
              key={group.title}
              className="rounded-2xl border border-[#DDE8E4] bg-white p-7 shadow-[0_12px_32px_rgba(15,47,38,0.05)]"
            >
              <div className="flex h-11 w-11 items-center justify-center rounded-xl border border-[#E8D9AF] bg-[#FBF4E2] text-sm font-bold text-[#C39A31]">
                {group.code}
              </div>
              <h2 className="mt-7 text-2xl font-bold text-[#10372D]">
                {group.title}
              </h2>
              <p className="mt-3 text-sm leading-relaxed text-[#4F6A61]">
                {group.description}
              </p>
              <div className="mt-6 space-y-3">
                {group.documents.map((document) => (
                  <div
                    key={document.id}
                    className="flex min-h-12 items-center justify-between gap-4 rounded-lg border border-[#DDE8E4] bg-[#EEF6F3] px-4 text-sm font-bold text-[#10372D]"
                  >
                    <span>{document.title}</span>
                    {group.actions && (
                      <span className="flex gap-5 text-[0.68rem] font-semibold text-[#B08A24]">
                        {document.file ? (
                          <>
                            <a href={document.file} target="_blank" rel="noopener noreferrer" className="hover:underline">View</a>
                            <a href={document.file} download className="hover:underline">Download</a>
                          </>
                        ) : (
                          <>
                            <span>View</span>
                            <span>Download</span>
                          </>
                        )}
                      </span>
                    )}
                  </div>
                ))}
              </div>
            </article>
          ))}
        </div>
      </section>

      <section className="container mx-auto px-4 pb-16 pt-2 sm:px-6 lg:px-8">
        <div
          className="rounded-3xl px-6 py-12 md:px-16"
          style={{
            background:
              "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
          }}
        >
          <h2 className="font-serif text-4xl font-medium text-white">
            {pgData.commitment_title1}{" "}
            <span className="italic text-[#D4AA3A]">{pgData.commitment_title2}</span>
          </h2>
          <p className="mt-6 max-w-5xl text-sm leading-relaxed text-[#D8E6E1]">
            {pgData.commitment_description}
          </p>
        </div>
      </section>
    </main>
  );
}
