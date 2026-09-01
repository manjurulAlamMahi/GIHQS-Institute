import { useParams } from "react-router"
import { useGetCatalogueByIdQuery } from "@/features/catalogue/api/catalogueApi"
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton"
import CourseOverview from "./components/CourseOverview"

const CourseDetails = () => {
  const { id } = useParams()
  const { data: response, isLoading } = useGetCatalogueByIdQuery(id as string, { skip: !id })

  if (isLoading) {
    return (
      <main className="bg-white min-h-screen w-full">
        <DocumentSkeleton />
      </main>
    )
  }

  const catalogue = response?.data?.catalogue

  if (!catalogue) {
    return <div className="p-8 text-center text-neutral-500">Catalogue not found</div>
  }

  return (
    <main className="bg-white h-full w-full">
      <CourseOverview catalogue={catalogue} />
    </main>
  )
}

export default CourseDetails