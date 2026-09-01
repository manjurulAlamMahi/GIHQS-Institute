import OfferingCard, { type OfferingCardProps } from "./OfferingCard"
import { useGetCataloguesQuery, useCreateCheckoutMutation } from "@/features/catalogue/api/catalogueApi"
import { ROUTES } from "@/routes/routes.constants"
import { Skeleton } from "@/components/ui/skeleton"
import { useSearchParams } from "react-router"
import { useState } from "react"
import { toast } from "sonner"

export default function OfferingGrid() {
  const [searchParams] = useSearchParams()
  const keyword = searchParams.get("keyword") || undefined
  const sorting = searchParams.get("sorting") || undefined
  const filtering = searchParams.get("filtering") || undefined

  const { data: response, isLoading } = useGetCataloguesQuery()
  const [createCheckout] = useCreateCheckoutMutation()
  const [checkingOutId, setCheckingOutId] = useState<string | null>(null)

  let catalogues = response?.data?.catalogues || []

  const handleCheckout = async (catalogueId: string) => {
    try {
      setCheckingOutId(catalogueId)
      const res = await createCheckout({ catalogue_id: parseInt(catalogueId) }).unwrap()
      if (res.success && res.data.redirect_url) {
        window.location.href = res.data.redirect_url
      }
    } catch (error: any) {
      console.error("Checkout failed:", error)
      toast.error(error?.data?.message || error?.message || "Checkout failed. Please try again.")
    } finally {
      setCheckingOutId(null)
    }
  }

  if (keyword) {
    const lowerKeyword = keyword.toLowerCase()
    catalogues = catalogues.filter(c => 
      c.title.toLowerCase().includes(lowerKeyword) || 
      c.short_description.toLowerCase().includes(lowerKeyword)
    )
  }

  if (sorting && sorting !== "all") {
    catalogues = catalogues.filter(c => c.service_type?.toLowerCase() === sorting.toLowerCase())
  }

  if (filtering) {
    if (filtering === "featured") {
      catalogues = catalogues.filter(c => c.is_feature)
    } else if (filtering === "trending") {
      catalogues = catalogues.filter(c => c.is_trending)
    } else if (filtering === "popular") {
      catalogues = catalogues.filter(c => c.is_popular)
    }
  }

  if (isLoading) {
    return (
      <div className="w-full py-4 font-sans antialiased">
        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {[...Array(6)].map((_, i) => (
            <main key={i} className="bg-white p-5 rounded-2xl border border-neutral-100/10 shadow-sm">
              <div className="rounded-2xl p-6 flex flex-col justify-between min-h-70 bg-neutral-100/60">
                <div>
                  <div className="flex items-center justify-between mb-5">
                    <Skeleton className="h-6 w-20 rounded-full" />
                    <Skeleton className="h-5 w-16 rounded-md" />
                  </div>

                  <Skeleton className="h-6 w-3/4 mb-2" />
                  <Skeleton className="h-6 w-1/2 mb-5" />

                  <Skeleton className="h-3 w-full mb-2" />
                  <Skeleton className="h-3 w-full mb-2" />
                  <Skeleton className="h-3 w-2/3 mb-6" />

                  <ul className="space-y-3 mb-6">
                    {[1, 2, 3].map((j) => (
                      <li key={j} className="flex items-center">
                        <Skeleton className="w-2 h-2 rounded-full mr-2.5 shrink-0" />
                        <Skeleton className="h-3 w-3/4" />
                      </li>
                    ))}
                  </ul>
                </div>
              </div>

              <div className="space-y-4 pt-4">
                <div className="flex flex-col items-end">
                  <Skeleton className="h-8 w-20 mb-2" />
                  <Skeleton className="h-3 w-36" />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <Skeleton className="h-10 w-full rounded-full" />
                  <Skeleton className="h-10 w-full rounded-full" />
                </div>
              </div>
            </main>
          ))}
        </div>
      </div>
    )
  }

  const mapCatalogueToOffering = (catalogue: any): OfferingCardProps => {
    const type = catalogue.service_type?.toLowerCase() as OfferingCardProps["type"] || "course";

    let statusBadge: OfferingCardProps["statusBadge"] = undefined;
    if (catalogue.is_feature) statusBadge = "featured";
    else if (catalogue.is_trending) statusBadge = "trending";
    else if (catalogue.is_popular) statusBadge = "popular";

    let colors = { bg: "#F3F4F6", primaryButton: "#374151", typeBadge: "#374151" };
    let actionText: OfferingCardProps["actionText"] = "Enroll";
    let actionTo: string | undefined = undefined;

    if (type === "certification") {
      colors = { bg: "#EDE5D1", primaryButton: "#A57C1B", typeBadge: "#A57C1B" };
      actionText = "Apply";
      actionTo = ROUTES.APPLY_CERTIFICATION;
    } else if (type === "course") {
      colors = { bg: "#D2E1E0", primaryButton: "#1A5C4A", typeBadge: "#1A5C4A" };
    } else if (type === "webinar") {
      colors = { bg: "#E8DBD3", primaryButton: "#8C4A22", typeBadge: "#8C4A22" };
    } else if (type === "module") {
      colors = { bg: "#F0E4ED", primaryButton: "#7C2A68", typeBadge: "#7C2A68" };
    } else if (type === "toolkit") {
      colors = { bg: "#E3E9F0", primaryButton: "#2A4B7C", typeBadge: "#2A4B7C" };
    }

    return {
      id: catalogue.id.toString(),
      type,
      statusBadge,
      title: catalogue.title,
      description: catalogue.short_description,
      features: catalogue.features.map((f: any) => f.description),
      price: `$${catalogue.price_regular}`,
      premiumPrice: `$${catalogue.price_member}`,
      actionText,
      actionTo,
      onActionClick: type !== "certification" ? () => handleCheckout(catalogue.id.toString()) : undefined,
      isActionLoading: checkingOutId === catalogue.id.toString(),
      colors,
    };
  };

  return (
    <div className="w-full py-4 font-sans antialiased">
      <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        {catalogues.map((item) => (
          <OfferingCard key={item.id} {...mapCatalogueToOffering(item)} />
        ))}
      </div>
    </div>
  )
}
