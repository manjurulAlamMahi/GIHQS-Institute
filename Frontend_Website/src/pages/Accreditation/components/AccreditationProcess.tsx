import SectionTitle from "./SectionTitle";
import { useGetAccreditationDetailsQuery } from "@/features/accreditation/api/accreditationApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function AccreditationProcess() {
  const { data: response, isLoading } = useGetAccreditationDetailsQuery();
  const processData = response?.data?.accreditation_processes;

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 pb-16 pt-6 sm:px-6 lg:px-8">
        <div className="space-y-10">
          <div className="flex justify-center">
            <Skeleton className="h-6 w-32 rounded-full bg-neutral-200" />
          </div>

          <div className="mx-auto max-w-3xl space-y-3 flex flex-col items-center">
            <Skeleton className="h-10 w-80 bg-neutral-200" />
            <Skeleton className="h-4 w-3/4 bg-neutral-100 mt-2" />
          </div>

          <div className="grid overflow-hidden rounded-lg border border-[#BCD2CB] bg-[#F7FAF9] md:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, index) => (
              <div
                key={index}
                className={`min-h-64 p-6 md:p-7 ${
                  index > 0 ? "border-t border-[#BCD2CB]" : ""
                } ${
                  index % 2 !== 0 ? "md:border-l md:border-t-0" : ""
                } ${
                  index >= 2 ? "md:border-t" : ""
                } ${
                  index % 3 !== 0 ? "lg:border-l" : "lg:border-l-0"
                } ${
                  index >= 3 ? "lg:border-t" : "lg:border-t-0"
                }`}
              >
                <Skeleton className="h-10 w-12 bg-neutral-200" />
                <div className="mt-6 space-y-2">
                  <Skeleton className="h-6 w-48 bg-neutral-200" />
                  <Skeleton className="h-3 w-32 bg-neutral-200/60" />
                </div>
                <div className="mt-5 space-y-2">
                  <Skeleton className="h-4 w-full bg-neutral-100" />
                  <Skeleton className="h-4 w-full bg-neutral-100" />
                  <Skeleton className="h-4 w-3/4 bg-neutral-100" />
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="container mx-auto px-4 pb-16 pt-6 sm:px-6 lg:px-8">
      <div className=" space-y-10">
        <SectionTitle label="Process" />

        <div className="mx-auto max-w-3xl space-y-3 text-center">
          <h2 className="text-3xl font-medium leading-tight text-[#0F2F26] md:text-4xl">
            {processData?.title1}{" "}
            <span className="font-serif italic text-[#D4AA3A]">{processData?.title2}</span>
          </h2>

          <p className="text-sm leading-relaxed text-[#4F6A61]">
            {processData?.description}
          </p>
        </div>

        <div className="grid overflow-hidden rounded-lg border border-[#BCD2CB] bg-[#F7FAF9] md:grid-cols-2 lg:grid-cols-3">
          {processData?.accreditation_process_features?.map((step, index) => (
            <article
              key={step.id}
              className={`min-h-64 p-6 md:p-7 ${
                index > 0 ? "border-t border-[#BCD2CB]" : ""
              } ${
                index % 2 !== 0 ? "md:border-l md:border-t-0" : ""
              } ${
                index >= 2 ? "md:border-t" : ""
              } ${
                index % 3 !== 0 ? "lg:border-l" : "lg:border-l-0"
              } ${
                index >= 3 ? "lg:border-t" : "lg:border-t-0"
              }`}
            >
              <p className="text-4xl font-light leading-none text-[#82AFA4]">
                {step.serial}
              </p>
              <div className="mt-6 space-y-1">
                <h3 className="font-serif text-xl font-medium leading-tight text-[#0F2F26]">
                  {step.title}
                </h3>
                <p className="text-xs font-bold leading-relaxed text-[#254C41]">
                  {step.subtitle}
                </p>
              </div>
              <p className="mt-5 text-sm leading-relaxed text-[#4F6A61]">
                {step.description}
              </p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
