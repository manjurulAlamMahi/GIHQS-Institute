import SectionTitle from "./SectionTitle";
import { useGetAccreditationDetailsQuery } from "@/features/accreditation/api/accreditationApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function AccreditationEligibility() {
  const { data: response, isLoading } = useGetAccreditationDetailsQuery();
  const eligibility = response?.data?.accreditation_eligibility;

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <div className="space-y-10">
          <div className="flex justify-center">
            <Skeleton className="h-6 w-32 rounded-full bg-neutral-200" />
          </div>

          <div className="mx-auto max-w-3xl space-y-3 flex flex-col items-center">
            <Skeleton className="h-10 w-64 bg-neutral-200" />
            <div className="space-y-2 w-full flex flex-col items-center mt-2">
              <Skeleton className="h-4 w-full bg-neutral-100" />
              <Skeleton className="h-4 w-5/6 bg-neutral-100" />
            </div>
          </div>

          <div className="grid overflow-hidden rounded-lg border border-[#BCD2CB] bg-[#F7FAF9] md:grid-cols-3">
            {Array.from({ length: 3 }).map((_, index) => (
              <div
                key={index}
                className={`min-h-36 p-6 space-y-3 ${
                  index > 0 ? "border-t border-[#BCD2CB] md:border-l md:border-t-0" : ""
                }`}
              >
                <Skeleton className="h-5 w-48 bg-neutral-200" />
                <div className="space-y-2 pt-2">
                  <Skeleton className="h-3 w-full bg-neutral-100" />
                  <Skeleton className="h-3 w-5/6 bg-neutral-100" />
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="container mx-auto px-4 py-14 sm:px-6 lg:px-8">
      <div className=" space-y-10">
        <SectionTitle label="Eligibility" />

        <div className="mx-auto max-w-3xl space-y-3 text-center">
          <h2 className="text-3xl font-medium leading-tight text-[#0F2F26] md:text-4xl">
            {eligibility?.title1}{" "}
            <span className="font-serif italic text-[#D4AA3A]">{eligibility?.title2}</span>
          </h2>

          <p className="text-sm leading-relaxed text-[#4F6A61]">
            {eligibility?.description}
          </p>
        </div>

        <div className="grid overflow-hidden rounded-lg border border-[#BCD2CB] bg-[#F7FAF9] md:grid-cols-3">
          {eligibility?.accreditation_eligibility_features?.map((item, index) => (
            <article
              key={item.id}
              className={`min-h-36 p-6 ${
                index > 0 ? "border-t border-[#BCD2CB] md:border-l md:border-t-0" : ""
              }`}
            >
              <h3 className="text-base font-bold leading-snug text-[#10372D]">
                {item.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-[#4F6A61]">
                {item.description}
              </p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
