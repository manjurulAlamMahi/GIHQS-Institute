
import { useGetAdvisoryServicesQuery } from "@/features/advisory/api/advisoryApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function TypicalDeliverables() {
    const { data: response, isLoading } = useGetAdvisoryServicesQuery();
    const deliverablesData = response?.data?.advisory_deliverable_cards;

    if (isLoading) {
        return (
            <div
                style={{
                    borderRadius: "24px",
                    background:
                        "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #FFF",
                }}
                className="w-full p-8 md:p-12 my-10 text-white shadow-xl font-sans"
            >
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div className="lg:col-span-6 space-y-4">
                        <Skeleton className="h-10 md:h-12 w-64 bg-white/20" />
                        <div className="space-y-2 mt-4">
                            <Skeleton className="h-4 w-full max-w-xl bg-white/20" />
                            <Skeleton className="h-4 w-5/6 max-w-xl bg-white/20" />
                            <Skeleton className="h-4 w-3/4 max-w-xl bg-white/20" />
                        </div>
                    </div>
                    <div className="lg:col-span-6 flex flex-wrap gap-3 lg:justify-end">
                        {Array.from({ length: 5 }).map((_, i) => (
                            <Skeleton key={i} className="h-10 w-40 rounded-full bg-white/20" />
                        ))}
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div
            style={{
                borderRadius: "24px",
                background:
                    "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #FFF",
            }}
            className="w-full p-8 md:p-12 my-10 text-white shadow-xl font-sans"
        >
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div className="lg:col-span-6 space-y-4">
                    <h2 className="text-3xl md:text-5xl font-serif font-normal tracking-tight">
                        {deliverablesData?.title1} <span className="italic text-[#F0D070] font-normal">{deliverablesData?.title2}</span>
                    </h2>
                    <p className="text-xs md:text-sm text-neutral-300 leading-relaxed font-light max-w-xl">
                        {deliverablesData?.description}
                    </p>
                </div>

                <div className="lg:col-span-6 flex flex-wrap gap-3 lg:justify-end">
                    {deliverablesData?.advisory_deliverable_card_features?.map((item) => (
                        <div
                            key={item.id}
                            className="px-5 py-2.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm transition-colors duration-200 hover:bg-white/10"
                        >
                            <span className="text-xs md:text-sm font-medium text-neutral-200 tracking-wide">
                                {item.name}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}