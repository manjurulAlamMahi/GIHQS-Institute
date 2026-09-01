import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Check } from "lucide-react";
import { useGetMembershipPackagesQuery, useMembershipCheckoutMutation } from "@/features/membership/api/membershipApi";
import { Skeleton } from "@/components/ui/skeleton";
import { toast } from "sonner";
import { useAppSelector } from "@/app/hooks";
import { useNavigate, useLocation } from "react-router";



type MembershipComparisonProps = {
  showIntro?: boolean
}

type CheckoutErrorResponse = {
  data?: {
    status?: boolean;
    message?: string;
    code?: number;
  }
}

export default function MembershipComparison({
  showIntro = true,
}: MembershipComparisonProps) {
  const { data: response, isLoading } = useGetMembershipPackagesQuery();
  const packages = response?.data?.membership_packages || [];
  const [processingPkgId, setProcessingPkgId] = useState<number | null>(null);
  
  const token = useAppSelector((state) => state.auth.token);
  const navigate = useNavigate();
  const location = useLocation();

  const [checkout, { isLoading: isCheckoutLoading }] = useMembershipCheckoutMutation();

  const handleCheckout = async (pkgId: number) => {
    if (!token) {
      navigate("/login", { state: { from: location } });
      return;
    }

    setProcessingPkgId(pkgId);
    try {
      const successUrl = `${window.location.origin}${window.location.pathname}?payment=success`;
      const cancelUrl = `${window.location.origin}${window.location.pathname}?payment=cancel`;
      
      const result: any = await checkout({ 
        membership_package_id: pkgId,
        success_url: successUrl,
        cancel_url: cancelUrl
      }).unwrap();
      
      if (result.success && result.data?.redirect_url) {
        window.location.href = result.data.redirect_url;
      } else if (result.status === false || result.success === false) {
        toast.error(result.message || "Checkout failed. Please try again.");
      }
    } catch (error) {
      console.error("Checkout failed:", error);
      const err = error as CheckoutErrorResponse;
      const errorMessage = err?.data?.message || "Checkout failed. Please try again.";
      toast.error(errorMessage);
    } finally {
      setProcessingPkgId(null);
    }
  };

  return (
    <section className="px-4 py-14 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl space-y-12">
        {showIntro && (
          <div className="mx-auto max-w-3xl space-y-5 text-center">
            <div className="inline-flex rounded-full border border-[#D4AA3A]/60 bg-white px-4 py-2 text-[0.68rem] font-semibold uppercase leading-none tracking-[0.16em] text-[#C39A31]">
              Membership Comparison
            </div>

            <div className="space-y-4">
              <h1 className="text-4xl font-medium leading-tight text-[#0F2F26] md:text-5xl">
                Standard vs{" "}
                <span className="font-serif italic text-[#D4AA3A]">
                  Premium
                </span>
              </h1>
              <p className="mx-auto max-w-2xl text-sm leading-relaxed text-[#3A5A50]">
                Choose the membership level that best supports your professional
                growth in healthcare quality, patient safety, accreditation, and
                responsible AI.
              </p>
            </div>

            {/* <div className="flex flex-col items-center justify-center gap-3 sm:flex-row">
              <Button
                asChild
                variant="outline"
                className="h-11 rounded-full border-[#0F4A3B]/35 bg-white px-6 text-sm font-bold text-[#0F2F26] hover:bg-[#EDF5F2]"
              >
                <Link to="/signup">Join as a Standard Member</Link>
              </Button>
              <Button
                asChild
                className="h-11 rounded-full bg-[#0F4A3B] px-6 text-sm font-bold text-white shadow-[0_14px_28px_rgba(15,74,59,0.20)] hover:bg-[#0A3328]"
              >
                <Link to="/signup">Become a Premium Member</Link>
              </Button>
            </div> */}
          </div>
        )}

        {isLoading ? (() => {
          const skeletonCount = packages.length > 0 ? packages.length : 2;
          const isJoinedSkel = skeletonCount === 2;
          
          return (
            <div 
              className={
                isJoinedSkel
                  ? "grid overflow-hidden rounded-3xl border border-[#E1E9E6] bg-white shadow-[0_18px_48px_rgba(15,47,38,0.10)] lg:grid-cols-2"
                  : `grid gap-6 md:gap-8 ${skeletonCount >= 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-1 max-w-md mx-auto'} md:grid-cols-2`
              }
            >
              {[...Array(skeletonCount)].map((_, idx) => {
                const isDark = isJoinedSkel ? idx === 1 : idx > 0;
                return (
                  <article 
                    key={`skel-${idx}`}
                    className={`relative overflow-hidden p-7 md:p-10 ${
                      !isJoinedSkel ? "rounded-3xl border border-[#E1E9E6] shadow-[0_18px_48px_rgba(15,47,38,0.10)]" : ""
                    } ${isDark ? "bg-transparent text-white" : "bg-white"}`}
                    style={isDark ? {
                      ...(isJoinedSkel && idx === 1 ? { borderRadius: "0 24px 24px 0" } : {}),
                      background: "radial-gradient(106.46% 66.57% at 67.39% 25.38%, rgba(212, 170, 58, 0.11) 0%, rgba(12, 42, 31, 0.11) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)"
                    } : {}}
                  >
                    <Skeleton className={`h-4 w-16 mb-4 ${isDark ? "bg-white/20" : ""}`} />
                    <Skeleton className={`h-8 w-48 mb-3 ${isDark ? "bg-white/20" : ""}`} />
                    <Skeleton className={`h-16 w-32 mb-4 ${isDark ? "bg-white/20" : ""}`} />
                    <Skeleton className={`h-4 w-full max-w-sm mb-2 ${isDark ? "bg-white/20" : ""}`} />
                    <Skeleton className={`h-4 w-3/4 max-w-sm mb-8 ${isDark ? "bg-white/20" : ""}`} />
                    
                    <div className={`space-y-4 divide-y pt-4 border-t ${isDark ? "divide-white/10 border-white/10" : "divide-[#DDE8E4] border-[#DDE8E4]"}`}>
                      {[...Array(8)].map((_, i) => (
                        <div key={`skel-feat-${idx}-${i}`} className="flex gap-3 pt-4">
                          <Skeleton className={`h-5 w-5 rounded-full shrink-0 ${isDark ? "bg-white/20" : ""}`} />
                          <div className="space-y-2 w-full">
                            <Skeleton className={`h-4 w-3/4 ${isDark ? "bg-white/20" : ""}`} />
                            <Skeleton className={`h-3 w-1/2 ${isDark ? "bg-white/20" : ""}`} />
                          </div>
                        </div>
                      ))}
                    </div>
                    <Skeleton className={`h-12 w-full rounded-full mt-10 ${isDark ? "bg-[#F4D36D]/40" : ""}`} />
                  </article>
                );
              })}
            </div>
          );
        })() : (
          <div 
            className={
              packages.length === 2
                ? "grid overflow-hidden rounded-3xl border border-[#E1E9E6] bg-white shadow-[0_18px_48px_rgba(15,47,38,0.10)] lg:grid-cols-2"
                : `grid gap-6 md:gap-8 ${packages.length >= 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-1 max-w-md mx-auto'} md:grid-cols-2`
            }
          >
            {packages.map((pkg, index) => {
              const isPaid = Number(pkg.price) > 0;
              const isJoined = packages.length === 2;
              
              return (
                <article
                  key={pkg.id}
                  className={`relative p-7 md:p-10 ${
                    !isJoined
                      ? "rounded-3xl border border-[#E1E9E6] shadow-[0_18px_48px_rgba(15,47,38,0.10)] overflow-hidden"
                      : ""
                  } ${isPaid ? "text-white overflow-hidden" : "bg-white"}`}
                  style={
                    isPaid
                      ? {
                          ...(isJoined && index === 1 ? { borderRadius: "0 24px 24px 0" } : {}),
                          background:
                            "radial-gradient(106.46% 66.57% at 67.39% 25.38%, rgba(212, 170, 58, 0.11) 0%, rgba(12, 42, 31, 0.11) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
                        }
                      : {}
                  }
                >
                  {isPaid && (
                    <div className="pointer-events-none absolute -right-20 top-11 w-80 rotate-45 bg-[#F0D070] py-4 text-center text-sm font-black uppercase leading-tight tracking-[0.12em] text-[#0F2F26] shadow-[0_18px_36px_rgba(244,211,109,0.18)]">
                      Certification
                      <br />
                      Candidates
                    </div>
                  )}

                  <p
                    className={`text-[0.68rem] font-semibold uppercase tracking-[0.24em] ${
                      isPaid ? "text-[#8FB3A6]" : "text-[#4F8B7A]"
                    }`}
                  >
                    {pkg.name}
                  </p>
                  <h2
                    className={`mt-4 text-3xl font-bold ${
                      isPaid ? "text-[#D4AA3A]" : "text-[#10372D]"
                    }`}
                  >
                    {pkg.title}
                  </h2>
                  <div className="mt-3 flex items-end gap-2">
                    <span
                      className={`text-6xl font-bold leading-none ${
                        isPaid ? "text-[#D4AA3A]" : "text-[#10372D]"
                      }`}
                    >
                      ${pkg.price}
                    </span>
                    <span
                      className={`pb-2 text-sm ${
                        isPaid ? "text-[#C7D8D2]" : "text-[#4F6A61]"
                      }`}
                    >
                      / year
                    </span>
                  </div>
                  <p
                    className={`mt-4 max-w-md text-sm leading-relaxed ${
                      isPaid ? "text-[#C7D8D2]" : "text-[#4F6A61]"
                    }`}
                  >
                    {pkg.short_description}
                  </p>

                  <div
                    className={`mt-8 divide-y ${
                      isPaid
                        ? "divide-white/10 border-t border-white/10"
                        : "divide-[#DDE8E4] border-t border-[#DDE8E4]"
                    }`}
                  >
                    {pkg.features.map((feature) => {
                      return (
                        <div key={feature.id} className="flex gap-3 py-4">
                          <span
                            className={`mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full ${
                              isPaid
                                ? "bg-[#D4AA3A]/15 text-[#D4AA3A]"
                                : "bg-[#DDEFEA] text-[#0F6B55]"
                            }`}
                          >
                            <Check className="h-3.5 w-3.5 stroke-3" />
                          </span>
                          <div>
                            <div className="flex flex-wrap items-center gap-2">
                              <h3
                                className={`text-sm font-bold ${
                                  isPaid
                                    ? "text-white"
                                    : "text-[#10372D]"
                                }`}
                              >
                                {feature.description}
                              </h3>
                              {feature.badge && (
                                <span className="rounded-full bg-[#F4D36D] px-2 py-0.5 text-[0.65rem] font-bold text-[#0F2F26]">
                                  {feature.badge}
                                </span>
                              )}
                            </div>
                            {feature.note && (
                              <p
                                className={`mt-1 text-xs ${
                                  isPaid ? "text-[#9FB7AF]" : "text-[#6F8580]"
                                }`}
                              >
                                {feature.note}
                              </p>
                            )}
                          </div>
                        </div>
                      );
                    })}
                  </div>

                  {isPaid && (
                    <Button
                      type="button"
                      onClick={() => handleCheckout(pkg.id)}
                      disabled={isCheckoutLoading && processingPkgId === pkg.id}
                      variant="default"
                      className="mt-10 h-12 w-full rounded-full text-sm font-bold bg-[#F4D36D] text-[#0F2F26] hover:bg-[#EBCB62]"
                    >
                      {isCheckoutLoading && processingPkgId === pkg.id ? "Processing..." : `Become a ${pkg.name} Member`}
                    </Button>
                  )}
                </article>
              );
            })}
          </div>
        )}
      </div>
    </section>
  );
}
