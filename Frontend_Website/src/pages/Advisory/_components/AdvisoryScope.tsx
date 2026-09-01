import { Card, CardContent } from "@/components/ui/card";
import { Shield } from "lucide-react";
import { useGetAdvisoryServicesQuery } from "@/features/advisory/api/advisoryApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function AdvisoryScope() {
    const { data: response, isLoading } = useGetAdvisoryServicesQuery();
    const scopes = response?.data?.advisory_scopes;

    if (isLoading) {
        return (
            <section className="w-full py-16 font-sans text-center">
                <div className="space-y-3 max-w-4xl mx-auto mb-12 flex flex-col items-center">
                    <Skeleton className="h-10 w-64 bg-neutral-200" />
                    <div className="space-y-2 w-full flex flex-col items-center mt-4">
                        <Skeleton className="h-4 w-full bg-neutral-100" />
                        <Skeleton className="h-4 w-11/12 bg-neutral-100" />
                        <Skeleton className="h-4 w-4/5 bg-neutral-100" />
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    {Array.from({ length: 4 }).map((_, idx) => (
                        <div key={idx} className="rounded-2xl border border-neutral-100 bg-white shadow-sm p-6 space-y-4">
                            <div className="p-0 space-y-3">
                                <Skeleton className="w-8 h-8 rounded-lg bg-neutral-200" />
                                <Skeleton className="h-6 w-48 bg-neutral-200" />
                                <div className="space-y-2 pt-1">
                                    <Skeleton className="h-3 w-full bg-neutral-100" />
                                    <Skeleton className="h-3 w-5/6 bg-neutral-100" />
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </section>
        );
    }

    const scopeCards = scopes?.advisory_scope_features?.map((feature) => {
        // Map string icons to components if they are actually SVG URLs in the DB, 
        // or just use the icon field if it's an image.
        // The JSON shows "icon": "https://..." so it is an image URL.
        return {
            id: feature.id,
            iconUrl: feature.icon,
            title: feature.title,
            desc: feature.description
        };
    }) || [];

    return (
        <section className="w-full py-16 font-sans text-center">
            {/* Title block info */}
            <div className="space-y-3 max-w-4xl mx-auto mb-12">
                <h2 className="text-3xl md:text-4xl font-serif text-[#0F2F26]">
                    {scopes?.title1} <span className="italic text-[#CAA24A]">{scopes?.title2}</span>
                </h2>
                <p className="text-xs md:text-sm text-neutral-500 font-light leading-relaxed">
                    {scopes?.description}
                </p>
            </div>

            {/* Grid container cards layout */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                {scopeCards.map((card) => (
                    <Card key={card.id} className="rounded-2xl border border-neutral-100 bg-white shadow-sm p-6 space-y-4">
                        <CardContent className="p-0 space-y-3">
                            <div className="w-8 h-8 rounded-lg bg-[#F7F0DF] flex items-center justify-center overflow-hidden p-1.5">
                                {card.iconUrl ? (
                                    <img src={card.iconUrl} alt="" className="w-full h-full object-contain" />
                                ) : (
                                    <Shield className="w-4 h-4 text-[#A57C1B]" />
                                )}
                            </div>
                            <h3 className="text-base md:text-lg font-bold tracking-tight text-neutral-900">
                                {card.title}
                            </h3>
                            <p className="text-xs md:text-sm text-neutral-500 font-light leading-relaxed">
                                {card.desc}
                            </p>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </section>
    );
}