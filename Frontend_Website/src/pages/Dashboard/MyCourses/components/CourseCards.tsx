import { Skeleton } from "@/components/ui/skeleton"
import { useGetPurchasedCataloguesQuery } from "@/features/profile/api/profileApi"
import { ROUTES } from "@/routes/routes.constants"
import { Link } from "react-router"

import { useSearchParams } from "react-router"

export function CourseCards() {
  const [searchParams] = useSearchParams()
  const serviceType = searchParams.get("service_type")
  const keyword = searchParams.get("keyword")

  const queryParams: Record<string, string> = {}
  if (keyword) queryParams.keyword = keyword

  const { data: response, isLoading } =
    useGetPurchasedCataloguesQuery(queryParams)
  const allCourses = response?.data?.catalogues || []

  const courses = allCourses.filter((course) => {
    const matchServiceType =
      serviceType && serviceType !== "all"
        ? course.service_type?.toLowerCase() === serviceType.toLowerCase()
        : true

    const matchKeyword = keyword
      ? (course.title?.toLowerCase() || "").includes(keyword.toLowerCase()) ||
        (course.short_title?.toLowerCase() || "").includes(
          keyword.toLowerCase()
        ) ||
        (course.short_description?.toLowerCase() || "").includes(
          keyword.toLowerCase()
        )
      : true

    return matchServiceType && matchKeyword
  })

  if (isLoading) {
    return (
      <div className="grid gap-7 xl:grid-cols-2">
        <Skeleton className="h-100 w-full rounded-[24px]" />
        <Skeleton className="h-100 w-full rounded-[24px]" />
      </div>
    )
  }

  if (courses.length === 0) {
    let emptyMessage = "You haven't purchased any courses yet."

    if (keyword && serviceType && serviceType !== "all") {
      emptyMessage = `No ${serviceType.toLowerCase()}s found matching "${keyword}".`
    } else if (keyword) {
      emptyMessage = `No results found matching "${keyword}".`
    } else if (serviceType && serviceType !== "all") {
      emptyMessage = `You haven't purchased any ${serviceType.toLowerCase()}s yet.`
    }

    return (
      <div className="flex min-h-75 items-center justify-center rounded-[24px] border border-border bg-white text-neutral-500">
        {emptyMessage}
      </div>
    )
  }

  return (
    <div className="grid gap-7 xl:grid-cols-2">
      {courses.map((course) => (
        <article
          key={course.id}
          className="rounded-[24px] border border-border bg-white p-5 shadow-sm"
        >
          <div className="min-h-57.5 rounded-[18px] bg-[#d2e1e0] p-5">
            <div className="mb-8 flex items-start justify-between gap-4">
              <span className="inline-flex h-8 items-center rounded-full bg-[#1a7568] px-4 text-[13px] font-bold tracking-wider text-white uppercase">
                {course.service_type || "Course"}
              </span>
              {course.is_feature && (
                <span className="inline-flex h-8 items-center rounded-full bg-[#fbf1cf] px-4 text-[13px] font-bold tracking-wider text-[#a57c1b] uppercase">
                  Featured
                </span>
              )}
            </div>

            <h2 className="font-serif text-[22px] leading-tight font-semibold text-black">
              {course.title}
            </h2>
            <p className="mt-4 line-clamp-2 max-w-130 text-[15px] leading-5 font-semibold text-black">
              {course.short_description}
            </p>

            {course.features && course.features.length > 0 && (
              <ul className="mt-4 space-y-2 text-[14px] font-medium text-[#111827]">
                {course.features.slice(0, 3).map((feature) => (
                  <li key={feature.id} className="flex items-center gap-2">
                    <span className="size-1.5 shrink-0 rounded-full bg-[#1a7568]" />
                    {feature.description}
                  </li>
                ))}
              </ul>
            )}
          </div>

          <div className="mt-5">
            <div className="mb-3 flex items-center justify-between">
              <span className="text-[14px] text-[#111827]">Progress 0%</span>
              <span className="inline-flex h-6 items-center rounded-full bg-[#f3f4f6] px-3 text-[13px] font-medium text-[#4b5563]">
                Not Started
              </span>
            </div>
            <div className="h-2 rounded-full bg-[#c6d8d1]">
              <div
                className="h-full rounded-full bg-[#166046]"
                style={{ width: "0%" }}
              />
            </div>
          </div>

          <div className="mt-6 grid grid-cols-2 gap-4">
            {/* <Link 
              to={ROUTES.FULL_MODULE.replace(":id", course.id.toString())}
              className="flex h-11 items-center justify-center rounded-full bg-[#14392f] px-5 text-[16px] font-medium text-white hover:bg-[#0f2f26]"
            >
              Continue
            </Link> */}
            <Link
              to={ROUTES.DASHBOARD_COURSE_DETAIL.replace(
                ":id",
                course.id.toString()
              )}
              className="flex h-11 items-center justify-center rounded-full bg-[#14392f] px-5 text-[16px] font-medium text-white hover:bg-[#0f2f26]"
            >
              View Details
            </Link>
          </div>
        </article>
      ))}
    </div>
  )
}
