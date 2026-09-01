import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from "@/components/ui/accordion";
import { useGetAdvisoryServicesQuery } from "@/features/advisory/api/advisoryApi";;
import { Skeleton } from "@/components/ui/skeleton";

export default function ServicePackages() {
    const { data: response, isLoading } = useGetAdvisoryServicesQuery();
    const services = response?.data?.advisory_services;

    if (isLoading) {
        return (
            <section className="w-full py-8 font-sans">
                <div className="space-y-3 max-w-4xl mx-auto text-center mb-12 flex flex-col items-center">
                    <Skeleton className="h-10 w-72 bg-neutral-200" />
                    <div className="space-y-2 w-full flex flex-col items-center mt-4">
                        <Skeleton className="h-4 w-full bg-neutral-100" />
                        <Skeleton className="h-4 w-11/12 bg-neutral-100" />
                    </div>
                </div>

                <div className="w-full space-y-4">
                    {Array.from({ length: 3 }).map((_, idx) => (
                        <div key={idx} className="border border-neutral-100 rounded-2xl bg-white shadow-sm px-6 py-4 flex items-center justify-between">
                            <div className="flex items-center gap-4 md:gap-6 text-left w-full">
                                <Skeleton className="w-10 h-10 rounded-xl bg-neutral-200 shrink-0" />
                                <div className="space-y-2 w-full max-w-md">
                                    <Skeleton className="h-3 w-24 rounded-md bg-neutral-200" />
                                    <Skeleton className="h-5 w-full bg-neutral-200" />
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </section>
        );
    }

    return (
        <section className="w-full py-8 font-sans">
            {/* Headline Header descriptions */}
            <div className="space-y-3 max-w-4xl mx-auto text-center mb-12">
                <h2 className="text-3xl md:text-4xl font-serif text-[#0F2F26]">
                    {services?.title1} <span className="italic text-[#CAA24A]">{services?.title2}</span>
                </h2>
                <p className="text-xs md:text-sm text-neutral-500 font-light leading-relaxed">
                    {services?.description}
                </p>
            </div>

            {/* Accordion List Component */}
            <Accordion type="single" collapsible className="w-full space-y-4">
                {services?.advisory_service_features?.map((pkg) => (
                    <AccordionItem
                        key={pkg.id}
                        value={pkg.id.toString()}
                        className="border border-neutral-100 rounded-2xl bg-white shadow-sm px-6 py-2 data-[state=open]:border-neutral-200 transition-all"
                    >
                        <AccordionTrigger className="hover:no-underline gap-4 flex items-center justify-between py-4">
                            <div className="flex items-center gap-4 md:gap-6 text-left">
                                {/* Numeric Index Badge indicator block */}
                                <div className="w-10 h-10 rounded-xl bg-[#f8faf9] flex items-center justify-center text-xs font-bold text-neutral-500 shrink-0">
                                    {pkg.serial_number}
                                </div>

                                {/* Content Info titles */}
                                <div className="space-y-1">
                                    <span className="inline-block text-[9px] font-bold tracking-widest text-[#A57C1B] bg-[#F7F0DF] px-2 py-0.5 rounded-md uppercase">
                                        {pkg.tagline}
                                    </span>
                                    <h3 className="text-base md:text-lg font-bold text-neutral-900 tracking-tight">
                                        {pkg.title}
                                    </h3>
                                </div>
                            </div>
                        </AccordionTrigger>

                        <AccordionContent className="text-xs md:text-sm text-neutral-500 font-light leading-relaxed pl-14 md:pl-16 pb-4">
                            {pkg.description}
                        </AccordionContent>
                    </AccordionItem>
                ))}
            </Accordion>
        </section>
    );
}