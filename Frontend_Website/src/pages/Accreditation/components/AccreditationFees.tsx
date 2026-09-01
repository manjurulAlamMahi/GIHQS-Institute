import { useGetAccreditationFeesQuery } from "@/features/accreditation/api/accreditationApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function AccreditationFees() {
  const { data: response, isLoading } = useGetAccreditationFeesQuery();
  const feeData = response?.data?.accreditation_fees;

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <div className="space-y-10">
          <div className="mx-auto max-w-4xl space-y-4 flex flex-col items-center">
            <Skeleton className="h-10 w-80 bg-neutral-200" />
            <div className="space-y-2 w-full max-w-3xl flex flex-col items-center">
              <Skeleton className="h-4 w-full bg-neutral-100" />
              <Skeleton className="h-4 w-5/6 bg-neutral-100" />
            </div>
          </div>

          <div className="grid gap-6 lg:grid-cols-3">
            {Array.from({ length: 3 }).map((_, index) => {
              const isFeatured = index === 1;
              return (
                <article
                  key={index}
                  className={
                    isFeatured
                      ? "rounded-xl border border-[#D4AA3A]/40 p-7 shadow-[0_18px_44px_rgba(15,47,38,0.18)]"
                      : "rounded-xl border border-[#E1E9E6] bg-white p-7 shadow-[0_16px_40px_rgba(15,47,38,0.08)]"
                  }
                  style={
                    isFeatured
                      ? {
                          background:
                            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
                        }
                      : undefined
                  }
                >
                  <Skeleton className={`h-6 w-3/4 ${isFeatured ? 'bg-white/20' : 'bg-neutral-200'}`} />
                  <Skeleton className={`mt-3 h-10 w-24 ${isFeatured ? 'bg-[#D4AA3A]/40' : 'bg-[#D4AA3A]/40'}`} />
                  <div className="mt-5 space-y-2">
                    <Skeleton className={`h-4 w-full ${isFeatured ? 'bg-white/10' : 'bg-neutral-100'}`} />
                    <Skeleton className={`h-4 w-5/6 ${isFeatured ? 'bg-white/10' : 'bg-neutral-100'}`} />
                  </div>
                  <ul className="mt-6 space-y-4">
                    {Array.from({ length: 3 }).map((_, i) => (
                      <li key={i} className="flex gap-2">
                        <Skeleton className={`h-2 w-2 rounded-full mt-1 ${isFeatured ? 'bg-white/20' : 'bg-neutral-300'}`} />
                        <Skeleton className={`h-4 w-full ${isFeatured ? 'bg-white/10' : 'bg-neutral-100'}`} />
                      </li>
                    ))}
                  </ul>
                </article>
              );
            })}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="container mx-auto px-4 py-14 sm:px-6 lg:px-8">
      <div className="space-y-10">
        <div className="mx-auto max-w-4xl space-y-4 text-center">
          <h2 className="text-3xl font-medium leading-tight text-[#0F2F26] md:text-4xl">
            {feeData?.title1}{" "}
            <span className="font-serif italic text-[#D4AA3A]">
              {feeData?.title2}
            </span>
          </h2>

          <p className="mx-auto max-w-3xl text-sm leading-relaxed text-[#4F6A61]">
            {feeData?.description}
          </p>
        </div>

        <div className="grid gap-6 lg:grid-cols-3">
          {feeData?.accreditation_fees_plans?.map((fee, index) => {
            const isFeatured = index === 1; // Assuming the middle plan is featured like before
            return (
              <article
                key={fee.id}
                className={
                  isFeatured
                    ? "rounded-xl border border-[#D4AA3A]/40 p-7 text-white shadow-[0_18px_44px_rgba(15,47,38,0.18)]"
                    : "rounded-xl border border-[#E1E9E6] bg-white p-7 shadow-[0_16px_40px_rgba(15,47,38,0.08)]"
                }
                style={
                  isFeatured
                    ? {
                        background:
                          "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
                      }
                    : undefined
                }
              >
                <h3
                  className={`text-xl font-bold leading-snug ${
                    isFeatured ? "text-white" : "text-[#10372D]"
                  }`}
                >
                  {fee.title}
                </h3>

                <p className="mt-3 text-4xl font-bold leading-none text-[#D4AA3A]">
                  ${fee.price}
                </p>

                <p
                  className={`mt-5 text-sm leading-relaxed ${
                    isFeatured ? "text-[#D8E6E1]" : "text-[#4F6A61]"
                  }`}
                >
                  {fee.description}
                </p>

                <ul
                  className={`mt-6 space-y-4 text-sm ${
                    isFeatured ? "text-[#D8E6E1]" : "text-[#4F6A61]"
                  }`}
                >
                  {fee.accreditation_fees_plan_features?.map((item) => (
                    <li key={item.id} className="flex gap-2">
                      <span aria-hidden="true">•</span>
                      <span>{item.feature}</span>
                    </li>
                  ))}
                </ul>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}
