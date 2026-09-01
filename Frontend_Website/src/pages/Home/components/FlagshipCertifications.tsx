import { ArrowRight } from "lucide-react";
import { useGetHomeFlagshipCertificationsQuery } from "@/features/home/api/homeApi";
import { Skeleton } from "@/components/ui/skeleton";
import { Link } from "react-router";
import { ROUTES } from "@/routes/routes.constants";

export default function FlagshipCertifications() {
  const { data: response, isLoading } = useGetHomeFlagshipCertificationsQuery();
  const data = response?.data?.home_recognized_pathways;
  const certificates = data?.home_certificates || [];

  return (
    <div className="relative w-full bg-[#f8faf9] px-0 py-16 font-sans overflow-hidden">
        {/* Section Header with thin horizontal lines */}
        <div className="relative z-10 flex items-center justify-center mb-14 w-full">
          <div className="h-px bg-[#d1dddb] grow max-w-xs md:max-w-md"></div>
          <span className="text-[#5b8276] text-xs font-semibold tracking-[0.25em] mx-4 whitespace-nowrap uppercase">
            Flagship Certifications
          </span>
          <div className="h-px bg-[#d1dddb] grow max-w-xs md:max-w-md"></div>
        </div>

        {/* Deep Green Outer Container Section */}
        <div className="bg-[#0F2F26] rounded-[24px] p-4 md:p-12 lg:p-16 relative z-10 overflow-hidden shadow-xl">
          <div
            className="pointer-events-none absolute inset-y-0 right-0 w-1/2 blur-3xl opacity-85"
            style={{
              background: `radial-gradient(140% 140% at 72% 50%, rgba(212, 170, 58, 0.22) 0%, rgba(212, 170, 58, 0.12) 24%, rgba(212, 170, 58, 0.06) 48%, rgba(15, 47, 38, 0) 80%)`,
            }}
          />
          
          {/* Top Info Header Grid Split */}
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 relative z-10">
            <div className="lg:col-span-6 space-y-2">
              {isLoading ? (
                <>
                  <Skeleton className="h-10 w-3/4 bg-white/10" />
                  <Skeleton className="h-10 w-1/2 bg-white/10" />
                </>
              ) : (
                <>
                  <h2 className="text-white text-3xl md:text-4xl font-normal tracking-tight font-serif">
                    {data?.title1 || "Recognized Pathways For"}
                  </h2>
                  <h2 className="text-[#D4AA3A] text-3xl md:text-4xl font-normal tracking-tight font-serif italic">
                    {data?.title2 || "Modern Healthcare Headers"}
                  </h2>
                </>
              )}
            </div>
            <div className="lg:col-span-6 flex items-center">
              {isLoading ? (
                <Skeleton className="h-20 w-full bg-white/10" />
              ) : (
                <p className="text-[#B8C5C0] text-sm md:text-base leading-relaxed max-w-xl lg:ml-auto">
                  {data?.description || "GIHQS certifications recognize professionals who demonstrate advanced knowledge and leadership in healthcare quality, patient safety, standards, compliance, clinical documentation, and the responsible use of AI in healthcare systems."}
                </p>
              )}
            </div>
          </div>

          {/* Three-Column Certificate Dynamic Content Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
            {isLoading ? (
              // Skeletons mimicking exact card layout
              Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="bg-white rounded-2xl overflow-hidden flex flex-col justify-between shadow-md border border-gray-500">
                  {/* Skeleton Upper Branding Block */}
                  <div className="p-8 pb-6 relative overflow-hidden" style={{ background: "linear-gradient(115deg, #0C2A1F 0%, #0F3828 40%, #1A5C4A 100%)" }}>
                    <Skeleton className="h-8 w-24 bg-white/20 mb-2" />
                    <Skeleton className="h-4 w-48 bg-white/20" />
                    <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-white/10" />
                  </div>

                  {/* Skeleton Lower Content Block */}
                  <div className="p-6 md:p-8 grow flex flex-col justify-between bg-white space-y-6">
                    <div className="space-y-4">
                      {/* Tagline */}
                      <div className="flex items-center space-x-2">
                        <Skeleton className="h-3.5 w-3.5 rounded-sm" />
                        <Skeleton className="h-3 w-28" />
                      </div>
                      
                      {/* Headline */}
                      <div className="space-y-2">
                        <Skeleton className="h-5 w-full" />
                        <Skeleton className="h-5 w-4/5" />
                      </div>

                      {/* Description */}
                      <div className="space-y-1.5 pt-1">
                        <Skeleton className="h-3 w-full" />
                        <Skeleton className="h-3 w-full" />
                        <Skeleton className="h-3 w-3/4" />
                      </div>

                      {/* FOR Callout */}
                      <div className="bg-[#f4f7f6] rounded-lg p-4 border border-[#e3eae8]">
                        <Skeleton className="h-2.5 w-10 mb-2.5" />
                        <Skeleton className="h-3 w-full mb-1.5" />
                        <Skeleton className="h-3 w-2/3" />
                      </div>
                    </div>

                    <div className="space-y-6 pt-2">
                      {/* Badges */}
                      <div className="flex flex-wrap gap-1.5">
                        <Skeleton className="h-[22px] w-24 rounded-full" />
                        <Skeleton className="h-[22px] w-20 rounded-full" />
                        <Skeleton className="h-[22px] w-28 rounded-full" />
                      </div>

                      {/* Link */}
                      <div className="border-t border-[#e3eae8] pt-4">
                        <Skeleton className="h-4 w-32" />
                      </div>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              certificates.map((cert) => (
                <div 
                  key={cert.id} 
                  className="bg-white rounded-2xl overflow-hidden flex flex-col justify-between shadow-md border border-gray-500"
                >
                  {/* Upper Colored Gradient Branding Block */}
                  <div 
                    className="p-8 pb-6 relative overflow-hidden"
                    style={{
                      background: "linear-gradient(115deg, #0C2A1F 0%, #0F3828 40%, #1A5C4A 100%)"
                    }}
                  >
                    {/* Subtle lighting overlay glow */}
                    <div 
                      className="absolute top-0 right-0 w-24 h-24 pointer-events-none" 
                      style={{
                        borderRadius: "55px",
                        background: "radial-gradient(70.71% 70.71% at 50% 50%, rgba(255, 255, 255, 0.10) 0%, rgba(255, 255, 255, 0.00) 70%)"
                      }}
                    />
                    
                    <h3 className="text-3xl font-semibold tracking-wide text-[#E2BA5A] mb-1 font-sans">
                      {cert.short_title}
                    </h3>
                    <p className="text-[#89A59C] text-xs font-medium leading-normal max-w-[90%]">
                      {cert.title}
                    </p>
                    
                    {/* Bottom divider line inside heading section */}
                    <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-[#D4AA3A]" />
                  </div>

                  {/* Lower White Description Content Block */}
                  <div className="p-6 md:p-8 grow flex flex-col justify-between bg-white space-y-6">
                    
                    <div className="space-y-4">
                      {/* Functional Context Mini Header Label */}
                      <div className="flex items-center space-x-2">
                        {cert.icon && <img src={cert.icon} alt="icon" className="w-3.5 h-3.5 object-contain" />}
                        <span className="text-[#5b8276] text-[10px] font-bold tracking-[0.15em] uppercase">
                          {cert.tagline}
                        </span>
                      </div>

                      {/* Headline sentence */}
                      <h4 className="text-[#1a2d27] font-semibold text-base leading-snug">
                        {cert.headline}
                      </h4>

                      {/* Paragraph description */}
                      <p className="text-[#3A5A50] text-xs md:text-sm leading-relaxed font-normal whitespace-pre-line">
                        {cert.description}
                      </p>

                      {/* TARGET/FOR Grey Context Callout Container */}
                      <div className="bg-[#f4f7f6] rounded-lg p-4 border border-[#e3eae8]">
                        <span className="text-[#728d83] text-[9px] font-bold tracking-wider block mb-1 uppercase">
                          FOR
                        </span>
                        <p className="text-[#234238] text-xs font-normal leading-relaxed">
                          {cert.audience}
                        </p>
                      </div>
                    </div>

                    {/* Badges Container and Navigation Trigger */}
                    <div className="space-y-6 pt-2">
                      <div className="flex flex-wrap gap-1.5">
                        {cert.tags?.split(',').map((badge, idx) => (
                          <span 
                            key={idx} 
                            className="px-2.5 py-1 bg-[#eaf2f0] text-[#3a5a4f] text-[10px] font-medium rounded-full"
                          >
                            {badge.trim()}
                          </span>
                        ))}
                      </div>

                      {/* Explore Link Anchor */}
                      <div className="border-t border-[#e3eae8] pt-4">
                        <Link 
                          to={ROUTES.CERTIFICATION}
                          className="inline-flex items-center text-xs md:text-sm font-bold text-[#1A5C4A] hover:text-[#316452] transition-colors duration-150 group"
                        >
                          <span className="mr-2">{cert.button_text}</span>
                          <ArrowRight className="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform duration-150" />
                        </Link>
                      </div>
                    </div>

                  </div>
                </div>
              ))
            )}
          </div>

        </div>

      </div>
  );
}