import { Button } from "@/components/ui/button";
import { ROUTES } from "@/routes/routes.constants";
import { Link } from "react-router";
import type { Catalogue } from "@/types/catalogue.types";
import { useCreateCheckoutMutation } from "@/features/catalogue/api/catalogueApi";
import { Loader2 } from "lucide-react";

import { toast } from "sonner";

interface Props {
  catalogue: Catalogue;
}

export default function CourseDetailHero({ catalogue }: Props) {
  const isCertification = catalogue.service_type?.toLowerCase() === "certification";
  const [createCheckout, { isLoading }] = useCreateCheckoutMutation();

  const handleCheckout = async () => {
    try {
      const response = await createCheckout({ catalogue_id: catalogue.id }).unwrap();
      if (response.success && response.data.redirect_url) {
        window.location.href = response.data.redirect_url;
      }
    } catch (error: any) {
      console.error("Checkout failed:", error);
      toast.error(error?.data?.message || error?.message || "Checkout failed. Please try again.");
    }
  };
  return (
    <div className="w-full pt-6 font-sans">
      {/* Premium dark green background container */}
      <div className="relative overflow-hidden rounded-3xl bg-linear-to-br from-[#0a2f1d] via-[#113f27] to-[#1b4d32] p-8 md:p-12 text-white shadow-md grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <div className="lg:col-span-2 space-y-6">
          {/* Taxonomy Tags Pill */}
          <div className="inline-block rounded-full bg-white/10 px-4 py-1 border border-white/5 backdrop-blur-sm">
            <span className="text-[10px] md:text-xs font-semibold tracking-widest uppercase text-yellow-500/90">
              {catalogue.service_type}
            </span>
          </div>

          {/* Title */}
          <h1 className="text-3xl md:text-5xl font-serif font-normal tracking-tight leading-tight">
            {catalogue.title}
          </h1>

          {/* Intro Blurb */}
          <p className="text-xs md:text-sm text-neutral-300 max-w-xl leading-relaxed font-light">
            {catalogue.short_description}
          </p>

          {/* Action Call to Actions */}
          <div className="flex flex-wrap items-center gap-3 pt-2">
            {isCertification ? (
               <Link to={ROUTES.APPLY_CERTIFICATION}>
                 <Button
                   type="button"
                   className="h-11 px-6 rounded-full bg-[#facc15] hover:bg-[#eab308] text-neutral-900 font-bold text-xs tracking-wide"
                 >
                   Apply Now
                 </Button>
               </Link>
            ) : (
               <Button
                 type="button"
                 onClick={handleCheckout}
                 disabled={isLoading}
                 className="h-11 px-6 rounded-full bg-[#facc15] hover:bg-[#eab308] text-neutral-900 font-bold text-xs tracking-wide flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
               >
                 {isLoading && <Loader2 className="w-4 h-4 animate-spin" />}
                 {isLoading ? "Redirecting..." : "Enroll Now"}
               </Button>
            )}
            <Link to={ROUTES.FULL_MODULE.replace(":id", catalogue.id.toString())}>
              <Button
                type="button"
                className="h-11 px-6 rounded-full bg-white/5 hover:bg-white/10 border border-white/20 text-white font-semibold text-xs tracking-wide transition-colors"
              >
                Full Module Page
              </Button>
            </Link>
            <Button
              type="button"
              className="h-11 px-6 rounded-full bg-white/5 hover:bg-white/10 border border-white/20 text-white font-semibold text-xs tracking-wide transition-colors"
            >
              Save for Later
            </Button>
          </div>
        </div>

        {/* Right Column: Information Stats Blocks & Financial metrics */}
        <div className="space-y-4 w-full">
          {/* Information Badges 2x2 Grid */}
          <div className="grid grid-cols-2 gap-3">
            <div className="bg-white/5 border border-white/5 rounded-xl p-4 backdrop-blur-sm">
              <span className="block text-[10px] uppercase tracking-wider text-neutral-400 font-medium">Duration</span>
              <span className="text-sm md:text-base font-bold text-white">4–6 hours</span>
            </div>
            <div className="bg-white/5 border border-white/5 rounded-xl p-4 backdrop-blur-sm">
              <span className="block text-[10px] uppercase tracking-wider text-neutral-400 font-medium">Level</span>
              <span className="text-sm md:text-base font-bold text-white">Intermediate</span>
            </div>
            <div className="bg-white/5 border border-white/5 rounded-xl p-4 backdrop-blur-sm">
              <span className="block text-[10px] uppercase tracking-wider text-neutral-400 font-medium">Lessons</span>
              <span className="text-sm md:text-base font-bold text-white">8 lessons</span>
            </div>
            <div className="bg-white/5 border border-white/5 rounded-xl p-4 backdrop-blur-sm">
              <span className="block text-[10px] uppercase tracking-wider text-neutral-400 font-medium">Access</span>
              <span className="text-sm md:text-base font-bold text-white">90 days</span>
            </div>
          </div>

          {/* Core Price Board */}
          <div className="bg-white/5 border border-white/5 rounded-xl p-5 backdrop-blur-sm space-y-1">
            <div className="flex items-baseline gap-2">
              <span className="text-3xl font-bold text-[#facc15]">${catalogue.price_regular}</span>
            </div>
            <p className="text-xs font-semibold tracking-wide text-neutral-200">
              Premium Members: ${catalogue.price_member}
            </p>
          </div>
        </div>

      </div>
    </div>
  );
}