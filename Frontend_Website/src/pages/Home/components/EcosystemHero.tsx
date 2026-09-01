import { Check } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Link } from "react-router";
import { useGetHomeServicesPathwaysQuery } from "@/features/home/api/homeApi";
import { Skeleton } from "@/components/ui/skeleton";
import { ROUTES } from "@/routes/routes.constants";

const bannerItems = [
  "Professional Certification",
  "Program Accreditation",
  "Healthcare Quality & Patient Safety",
];

export default function EcosystemHero() {
  const { data: response, isLoading } = useGetHomeServicesPathwaysQuery();
  const gihqs = response?.data?.home_gihqs;

  const ecosystemData = gihqs ? [
    {
      category: gihqs.learning_tagline,
      title: gihqs.learning_title,
      description: gihqs.learning_details,
    },
    {
      category: gihqs.certificate_tagline,
      title: gihqs.certificate_title,
      description: gihqs.certificate_details,
    },
    {
      category: gihqs.lead_tagline,
      title: gihqs.lead_title,
      description: gihqs.lead_details,
    },
  ] : [];

  if (isLoading) {
    return (
      <div className="w-full bg-[#f8faf9] py-8 font-sans">
        <div className="w-full overflow-hidden rounded-[32px] bg-[#0F2F26] relative">
          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 p-4 md:p-8 lg:p-16">
            
            {/* Skeleton Left Section */}
            <div className="lg:col-span-7 flex flex-col justify-center space-y-8">
              <div>
                <Skeleton className="h-7 w-72 rounded-full bg-white/20" />
              </div>
              
              <div className="space-y-3">
                <Skeleton className="h-10 md:h-12 w-full max-w-xl bg-white/20" />
                <Skeleton className="h-10 md:h-12 w-3/4 max-w-md bg-white/20" />
              </div>
              
              <div className="space-y-2">
                <Skeleton className="h-4 w-full max-w-lg bg-white/20" />
                <Skeleton className="h-4 w-5/6 max-w-lg bg-white/20" />
                <Skeleton className="h-4 w-4/5 max-w-lg bg-white/20" />
              </div>

              <div className="flex flex-wrap gap-3 pt-2">
                <Skeleton className="h-11.5 w-45 rounded-full bg-white/20" />
                <Skeleton className="h-11.5 w-40 rounded-full bg-white/20" />
                <Skeleton className="h-11.5 w-37.5 rounded-full bg-white/20" />
                <Skeleton className="h-11.5 w-55 rounded-full bg-white/20" />
              </div>
            </div>

            {/* Skeleton Right Section */}
            <div className="lg:col-span-5 flex flex-col justify-center relative lg:pl-8">
              <div className="hidden lg:block absolute left-0 top-4 bottom-4 w-px bg-white/10" />
              
              <div className="space-y-8 lg:pl-8">
                <Skeleton className="h-3 w-48 bg-white/20" />
                
                <div className="space-y-6">
                  {Array.from({ length: 3 }).map((_, idx) => (
                    <div key={idx} className={`space-y-3 pt-6 ${idx !== 0 ? "border-t border-white/10" : "pt-0"}`}>
                      <Skeleton className="h-2.5 w-32 bg-white/20" />
                      <Skeleton className="h-7 w-64 bg-white/20" />
                      <div className="space-y-1.5 pt-1">
                        <Skeleton className="h-3 w-full max-w-sm bg-white/20" />
                        <Skeleton className="h-3 w-5/6 max-w-sm bg-white/20" />
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
            
          </div>
          
          {/* Skeleton Bottom Bar */}
          <div className="w-full bg-[#EAF2F0] py-5 px-6 md:px-16 flex flex-wrap justify-start md:justify-center items-center gap-x-10 gap-y-3">
             {Array.from({ length: 3 }).map((_, idx) => (
               <div key={idx} className="flex items-center space-x-2.5">
                 <Skeleton className="w-5 h-5 rounded-full bg-[#c2d3cd]" />
                 <Skeleton className="h-4 w-40 bg-[#c2d3cd]" />
               </div>
             ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="w-full bg-[#f8faf9] py-8 font-sans">
      <div className="w-full overflow-hidden rounded-[32px] bg-[#0F2F26] relative">

        <div
          className="pointer-events-none absolute inset-y-0 right-0 w-1/2 blur-3xl opacity-85"
          style={{
            background: `radial-gradient(140% 140% at 72% 50%, rgba(212, 170, 58, 0.22) 0%, rgba(212, 170, 58, 0.12) 24%, rgba(212, 170, 58, 0.06) 48%, rgba(15, 47, 38, 0) 80%)`,
          }}
        />

        <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 p-4 md:p-8 lg:p-16">

          <div className="lg:col-span-7 flex flex-col justify-center space-y-8">

            <div>
              <div
                className="rounded-full border border-[rgba(240,208,112,0.72)] bg-[rgba(240,208,112,0.10)] inline-flex items-center px-1 md:px-3.5 py-1.75 text-[#D4AA3A] text-xs font-normal md:font-semibold tracking-wider uppercase leading-none"
              >
                {gihqs?.tagline || "Global Institute for Healthcare Quality & Safety"}
              </div>
            </div>

            <h1 className="text-3xl md:text-5xl font-normal text-white leading-[1.15] tracking-tight max-w-2xl">
              {gihqs?.title1}{" "}
              <span className="font-serif italic text-[#D4AA3A]">
                {gihqs?.title2}
              </span>
            </h1>

            <p className="text-[#B8C5C0] text-sm md:text-base leading-relaxed font-normal max-w-xl opacity-90">
              {gihqs?.description}
            </p>

            <div className="flex flex-wrap gap-3 pt-2">
              <Button
                asChild
                className="hover:bg-[#dfba5f] border-[#2c4e44] bg-[#16382e] text-white font-medium text-xs rounded-full px-6 py-5 transition-colors duration-150"
              >
                <Link to={ROUTES.CERTIFICATION}>
                  {gihqs?.certificate_btn_text || "Certifications"}
                </Link>
              </Button>

              <Button
                asChild
                variant="outline"
                className="hover:bg-[#dfba5f] border-[#2c4e44] bg-[#16382e] text-white font-medium text-xs rounded-full px-6 py-5 transition-colors duration-150"
              >
                <Link to={ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE}>
                  {gihqs?.learning_btn_text || "Learning Catalogue"}
                </Link>
              </Button>

              <Button
                asChild
                variant="outline"
                className="hover:bg-[#dfba5f] border-[#2c4e44] bg-[#16382e] text-white font-medium text-xs rounded-full px-6 py-5 transition-colors duration-150"
              >
                <Link to={ROUTES.ADVISORY}>
                  {gihqs?.advisory_btn_text || "Advisory Services"}
                </Link>
              </Button>

              <Button
                asChild
                variant="outline"
                className="hover:bg-[#dfba5f] border-[#2c4e44] bg-[#16382e] text-white font-medium text-xs rounded-full px-6 py-5 transition-colors duration-150"
              >
                <Link to={ROUTES.MEMBERSHIP}>
                  {gihqs?.member_btn_text || "Membership"}
                </Link>
              </Button>
            </div>
          </div>

          <div className="lg:col-span-5 flex flex-col justify-center relative lg:pl-8">

            <div className="hidden lg:block absolute left-0 top-4 bottom-4 w-px bg-[#CAA24A99]/60" />

            <div className="space-y-8 lg:pl-8">
              {/* Feature Box Context Title */}
              <h2 className="text-[#D4AA3A] text-xs font-semibold tracking-[0.22em] uppercase">
                {gihqs?.professional_ecosystem_title || "GIHQS Professional Ecosystem"}
              </h2>

              {/* Dynamic Feature Separator Rendering Grid */}
              <div className="space-y-6">
                {ecosystemData.map((item, index) => (
                  <div
                    key={index}
                    className={`space-y-2 pt-6 ${index !== 0 ? "border-t border-[#CAA24A99]/60" : "pt-0"}`}
                  >
                    <span className="text-[#8FA89F] text-[10px] font-bold tracking-[0.15em] uppercase block">
                      {item.category}
                    </span>
                    <h3 className="text-white text-2xl font-semibold tracking-wide">
                      {item.title}
                    </h3>
                    <p className="text-[#8FA89F] text-sm font-normal leading-normal max-w-sm">
                      {item.description}
                    </p>
                  </div>
                ))}
              </div>
            </div>

          </div>
        </div>

        {/* BOTTOM SUB-BAR: Content Validation Row */}
        <div className="w-full bg-[#EAF2F0] border-t border-white/5 py-5 px-6 md:px-16">
          <div className="flex flex-wrap justify-start md:justify-center items-center gap-x-10 gap-y-3">
            {bannerItems.map((text, idx) => (
              <div key={idx} className="flex items-center space-x-2.5">
                <div className="bg-[#0F2F26] rounded-full p-1 flex items-center justify-center shrink-0">
                  <Check className="w-3 h-3 text-white stroke-[3.5]" />
                </div>
                <span className="text-[#2B473E] text-xs md:text-sm font-semibold tracking-wide">
                  {text}
                </span>
              </div>
            ))}
          </div>
        </div>

      </div>
    </div>
  );
}