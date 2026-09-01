import { CourseCards } from "./components/CourseCards"
import { CourseFilters } from "./components/CourseFilters"
import { CourseStats } from "./components/CourseStats"

const MyCoursesPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <div className="space-y-6">
        <CourseStats />
        <CourseFilters />
        <CourseCards />
      </div>
    </section>
  )
}

export default MyCoursesPage
