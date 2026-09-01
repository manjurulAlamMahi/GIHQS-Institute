
import { useGetRequestAdvisoryConsultationQuery } from "@/features/advisory/api/advisoryApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function RequestAdvisoryHero() {
    const { data: response, isLoading } = useGetRequestAdvisoryConsultationQuery();
    const heroData = response?.data?.request_advisories;

    if (isLoading) {
        return (
            <div className="w-full mx-auto pt-6 font-sans">
                <div
                    style={{
                        borderRadius: "24px",
                        background:
                            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
                    }}
                    className="p-8 md:p-12 text-white shadow-lg space-y-4 relative overflow-hidden"
                >
                    <Skeleton className="h-6 w-40 rounded-full bg-white/20" />
                    
                    <div className="space-y-2 mt-4">
                        <Skeleton className="h-10 md:h-12 w-80 bg-white/20" />
                    </div>

                    <div className="space-y-2 pt-2">
                        <Skeleton className="h-4 w-full max-w-2xl bg-white/20" />
                        <Skeleton className="h-4 w-4/5 max-w-2xl bg-white/20" />
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="w-full mx-auto pt-6 font-sans">
            <div
                style={{
                    borderRadius: "24px",
                    background:
                        "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
                }}
                className="p-8 md:p-12 text-white shadow-lg space-y-4 relative overflow-hidden"
            >
                <div className="inline-block rounded-full bg-white/10 px-3 py-1 border border-white/5 backdrop-blur-sm">
                    <span className="text-[10px] md:text-xs font-semibold tracking-widest uppercase text-yellow-500/90">
                        {heroData?.tagline}
                    </span>
                </div>

                <h1 className="text-3xl md:text-5xl font-serif font-normal tracking-tight leading-tight">
                    {heroData?.title1} <span className="italic text-[#F0D070] font-normal">{heroData?.title2}</span>
                </h1>

                <p className="text-xs md:text-sm text-neutral-300 leading-relaxed font-light max-w-4xl pt-1">
                    {heroData?.description}
                </p>
            </div>
        </div>
    );
}