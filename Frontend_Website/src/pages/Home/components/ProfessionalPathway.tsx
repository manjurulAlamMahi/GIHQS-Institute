import { ArrowRight } from "lucide-react";
import { useGetHomeServicesPathwaysQuery } from "@/features/home/api/homeApi";
import { Skeleton } from "@/components/ui/skeleton";
import { Link } from "react-router";
import { ROUTES } from "@/routes/routes.constants";

export default function ProfessionalPathway() {
  const { data: response, isLoading } = useGetHomeServicesPathwaysQuery();
  const pathwayData = response?.data?.home_gihqs?.home_professional_pathways || [];

  return (
    <div className="w-full bg-[#f8faf9] py-16 font-sans">
      <div className="container mx-auto">
        
        {/* Section Header with split horizontal lines */}
        <div className="flex items-center justify-center mb-10 w-full">
          <div className="h-px bg-[#d1dddb] grow max-w-xs md:max-w-md"></div>
          <span className="text-[#5b8276] text-xs font-semibold tracking-[0.25em] mx-4 whitespace-nowrap uppercase">
            The GIHQS Professional Pathway
          </span>
          <div className="h-px bg-[#d1dddb] grow max-w-xs md:max-w-md"></div>
        </div>

        {/* Main Grid Container - sharp corners (no rounded classes) */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 border border-[#d1dddb] bg-white overflow-hidden divide-y divide-[#d1dddb] md:divide-y-0 md:divide-x">
          {isLoading ? (
            Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="flex flex-col justify-between p-8 min-h-[420px] bg-[#fdfefe]">
                <div>
                  <Skeleton className="h-12 w-12 mb-6" />
                  <Skeleton className="h-8 w-48 mb-4" />
                  <Skeleton className="h-20 w-full" />
                </div>
                <div className="mt-8 pt-4 min-h-8">
                  <Skeleton className="h-4 w-32" />
                </div>
              </div>
            ))
          ) : pathwayData.map((item, index) => (
            <div
              key={item.serial}
              className="flex flex-col justify-between p-8 min-h-[420px] bg-[#fdfefe] transition-colors duration-200 hover:bg-[#f4f7f6]"
            >
              <div>
                {/* Large Subtle Numeric Identifier */}
                <div className="text-5xl font-light tracking-tight mb-6 opacity-80" style={{ color: "var(--primary-300, #8AA89C)" }}>
                  {item.serial}
                </div>

                {/* Primary Card Heading */}
                <h3 className="text-2xl font-normal font-serif mb-4 leading-snug" style={{ color: "var(--primary-900, #0F2F26)" }}>
                  {item.title}
                </h3>

                {/* Context / Informational Text */}
                <p className="text-sm font-normal leading-relaxed tracking-normal" style={{ color: "var(--primary-700, #3A5A50)" }}>
                  {item.description}
                </p>
              </div>

              {/* Action Link */}
              <div className="mt-8 pt-4 min-h-8">
                {item.link_text && (
                  <Link
                    to={
                      index === 0
                        ? ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE
                        : index === 1
                        ? ROUTES.CERTIFICATION
                        : index === 2
                        ? ROUTES.MEMBERSHIP
                        : ROUTES.CERTIFICATION
                    }
                    className="inline-flex items-center text-sm font-semibold hover:text-[#3a6356] transition-colors duration-150 group"
                    style={{ color: "var(--primary-800, #1A3C32)" }}
                  >
                    <span className="mr-2">{item.link_text}</span>
                    <ArrowRight className="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-150" />
                  </Link>
                )}
              </div>

            </div>
          ))}
        </div>

        {/* Bottom Banner Summary Narrative */}
        <div className="mt-6 text-left">
          <p className="text-xs md:text-sm font-semibold text-[#006045] tracking-wide leading-relaxed">
            An integrated pathway from professional learning to certification, continuing education, and leadership in high-reliability healthcare systems.
          </p>
        </div>

      </div>
    </div>
  );
}