import SectionTitle from "./SectionTitle";
import { useGetAccreditationDetailsQuery } from "@/features/accreditation/api/accreditationApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function AccreditationDomains() {
  const { data: response, isLoading } = useGetAccreditationDetailsQuery();
  const domainData = response?.data?.accreditation_domains;

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <div className="space-y-10">
          <div className="flex justify-center">
            <Skeleton className="h-6 w-40 rounded-full bg-neutral-200" />
          </div>

          <div className="mx-auto max-w-3xl space-y-3 flex flex-col items-center">
            <Skeleton className="h-10 w-80 bg-neutral-200" />
            <Skeleton className="h-4 w-3/4 bg-neutral-100 mt-2" />
          </div>

          <div className="grid gap-6 lg:grid-cols-2">
            {Array.from({ length: 10 }).map((_, i) => (
              <article
                key={i}
                className="rounded-xl border border-[#D8E5E1] bg-white p-6 shadow-[0_8px_24px_rgba(15,47,38,0.06)]"
              >
                <div className="mb-5 flex items-center gap-3">
                  <Skeleton className="h-8 w-8 rounded-full bg-neutral-200" />
                  <Skeleton className="h-3 w-24 bg-neutral-200/60" />
                </div>
                <Skeleton className="h-6 w-3/4 bg-neutral-200" />
                <div className="mt-4 space-y-2">
                  <Skeleton className="h-4 w-full bg-neutral-100" />
                  <Skeleton className="h-4 w-full bg-neutral-100" />
                  <Skeleton className="h-4 w-5/6 bg-neutral-100" />
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="container mx-auto px-4 py-14 sm:px-6 lg:px-8">
      <div className="space-y-10">
        <SectionTitle label="Standards Domains" />

        <div className="mx-auto max-w-3xl space-y-3 text-center">
          <h2 className="text-3xl font-medium leading-tight text-[#0F2F26] md:text-4xl">
            {domainData?.title1}{" "}
            <span className="font-serif italic text-[#D4AA3A]">
              {domainData?.title2}
            </span>
          </h2>

          <p className="text-sm leading-relaxed text-[#4F6A61]">
            {domainData?.description}
          </p>
        </div>

        <div className="grid gap-6 lg:grid-cols-2">
          {domainData?.accreditation_domain_features?.map((domain) => (
            <article
              key={domain.id}
              className="rounded-xl border border-[#D8E5E1] bg-white p-6 shadow-[0_8px_24px_rgba(15,47,38,0.06)]"
            >
              <div className="mb-5 flex items-center gap-3">
                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-[#315F52] text-xs font-bold text-white">
                  {domain.domain_serial.replace('Domain ', '')}
                </span>
                <span className="text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-[#C39A31]">
                  {domain.domain_serial}
                </span>
              </div>

              <h3 className="text-xl font-bold leading-snug text-[#152E29]">
                {domain.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-[#4F6A61]">
                {domain.description}
              </p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
