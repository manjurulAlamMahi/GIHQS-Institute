import { Button } from "@/components/ui/button"
import { useGetAdvisoryServicesQuery } from "@/features/advisory/api/advisoryApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Link } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

export default function AdvisoryNeeds() {
  const { data: response, isLoading } = useGetAdvisoryServicesQuery()
  const needsData = response?.data?.advisory_discuss_cards

  if (isLoading) {
    return (
      <div
        style={{
          borderRadius: "24px",
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #FFF",
        }}
        className="my-10 w-full p-8 text-center font-sans text-white shadow-xl md:p-14"
      >
        <div className="mx-auto flex max-w-4xl flex-col items-center space-y-6">
          <Skeleton className="h-10 w-80 bg-white/20 md:h-12" />

          <div className="mt-2 flex w-full max-w-3xl flex-col items-center space-y-2">
            <Skeleton className="h-4 w-full bg-white/20" />
            <Skeleton className="h-4 w-11/12 bg-white/20" />
            <Skeleton className="h-4 w-4/5 bg-white/20" />
          </div>

          <div className="pt-4">
            <Skeleton className="h-11 w-56 rounded-full bg-[#F0D070]/60" />
          </div>
        </div>
      </div>
    )
  }

  return (
    <div
      style={{
        borderRadius: "24px",
        background:
          "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #FFF",
      }}
      className="my-10 w-full p-8 text-center font-sans text-white shadow-xl md:p-14"
    >
      <div className="mx-auto max-w-4xl space-y-6">
        <h2 className="font-serif text-3xl font-normal tracking-tight md:text-4xl">
          {needsData?.title1}{" "}
          <span className="font-normal text-[#F0D070] italic">
            {needsData?.title2}
          </span>
        </h2>

        <p className="mx-auto max-w-3xl px-2 text-xs leading-relaxed font-light text-neutral-200 md:text-sm">
          {needsData?.description}
        </p>

        <div className="pt-4">
          <Button
            asChild
            style={{ backgroundColor: "#F0D070" }}
            className="h-11 rounded-full border-none px-7 text-xs font-bold tracking-wide text-black shadow-none transition-all duration-200 hover:scale-[1.02] hover:bg-[#ebd48a]"
          >
            <Link to={ROUTES.REQUEST_ADVISORY_CONSULTATION}>
              {needsData?.button_text || "Request Advisory Services"}
            </Link>
          </Button>
        </div>
      </div>
    </div>
  )
}
