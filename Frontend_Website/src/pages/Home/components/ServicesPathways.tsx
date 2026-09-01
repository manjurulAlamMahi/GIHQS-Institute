import { ArrowRight } from "lucide-react";
import { useGetHomeServicesPathwaysQuery } from "@/features/home/api/homeApi";
import { Skeleton } from "@/components/ui/skeleton";
import { Link } from "react-router";
import { ROUTES } from "@/routes/routes.constants";

export default function ServicesAndPathways() {
  const { data: response, isLoading } = useGetHomeServicesPathwaysQuery();
  const servicesData = response?.data?.home_gihqs?.home_services_pathways || [];

  return (
    <div className="w-full bg-[#f8faf9] py-16 font-sans">
      <div className="container mx-auto">
        
        {/* Section Header with split horizontal lines */}
        <div className="flex items-center justify-center mb-10 w-full">
          <div className="h-[1px] bg-[#d1dddb] grow max-w-xs md:max-w-md"></div>
          <span className="text-[#5b8276] text-xs font-semibold tracking-[0.25em] mx-4 whitespace-nowrap uppercase">
            Services & Pathways
          </span>
          <div className="h-[1px] bg-[#d1dddb] grow max-w-xs md:max-w-md"></div>
        </div>

        {/* Main Grid Container - sharp corners (no rounded classes) */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 border border-[#d1dddb] bg-white overflow-hidden divide-y divide-[#d1dddb] md:divide-y-0 md:divide-x">
          {isLoading ? (
            Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="flex flex-col justify-between p-8 min-h-[420px] bg-[#fdfefe]">
                <div>
                  <Skeleton className="h-12 w-12 mb-6" />
                  <Skeleton className="h-4 w-32 mb-3" />
                  <Skeleton className="h-8 w-48 mb-4" />
                  <Skeleton className="h-20 w-full" />
                </div>
                <div className="mt-8 pt-4">
                  <Skeleton className="h-4 w-32" />
                </div>
              </div>
            ))
          ) : servicesData.map((service, index) => (
            <div
              key={service.serial}
              className="flex flex-col justify-between p-8 min-h-[420px] bg-[#fdfefe] transition-colors duration-200 hover:bg-[#f4f7f6]"
            >
              <div>
                {/* Large Subtle Numeric Identifier */}
                <div className="text-5xl font-light text-[#8ba39c] tracking-tight mb-6 opacity-80">
                  {service.serial}
                </div>

                {/* Targeted Audience Subtitle */}
                <div className="text-[10px] font-bold text-[#2D7D6A] tracking-[0.15em] uppercase mb-3">
                  {service.target_audience}
                </div>

                {/* Primary Card Heading */}
                <h3 className="text-2xl font-normal text-[#000000] font-serif mb-4 leading-snug">
                  {service.title}
                </h3>

                {/* Context / Informational Text */}
                <p className="text-sm text-[#3A5A50] font-normal leading-relaxed tracking-normal">
                  {service.description}
                </p>
              </div>

              {/* Action Link / Anchor Button */}
              <div className="mt-8 pt-4">
                <Link
                  to={
                    index === 0
                      ? ROUTES.CERTIFICATION
                      : index === 1
                      ? ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE
                      : index === 2
                      ? ROUTES.ACCREDITATION
                      : index === 3
                      ? ROUTES.ADVISORY
                      : ROUTES.HOME
                  }
                  className="inline-flex items-center text-sm font-semibold text-[#1A5C4A] hover:text-[#3a6356] transition-colors duration-150 group"
                >
                  <span className="mr-2">{service.link_text}</span>
                  <ArrowRight className="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-150" />
                </Link>
              </div>

            </div>
          ))}
        </div>

      </div>
    </div>
  );
}