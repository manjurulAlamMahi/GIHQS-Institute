import { useParams } from "react-router"
import { useGetCatalogueByIdQuery } from "@/features/catalogue/api/catalogueApi"
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton"
import AutoIframe from "@/components/shared/AutoIframe"

const CourseStoryGuide = () => {
  const { id } = useParams()
  const { data: response, isLoading } = useGetCatalogueByIdQuery(id as string, {
    skip: !id,
  })

  if (isLoading) {
    return (
      <main className="min-h-screen w-full bg-white">
        <DocumentSkeleton />
      </main>
    )
  }

  const catalogue = response?.data?.catalogue

  if (!catalogue) {
    return (
      <div className="p-8 text-center text-neutral-500">
        Catalogue not found
      </div>
    )
  }

  // story_guide_file is only returned to a user who owns the catalogue.
  if (!catalogue.story_guide_file) {
    return (
      <main className="flex min-h-screen w-full items-center justify-center bg-white px-6">
        <p className="text-center text-neutral-500">
          {catalogue.has_access === false
            ? `Enrol in ${catalogue.title} to read the story guide.`
            : "No story guide available for this catalogue."}
        </p>
      </main>
    )
  }

  return (
    <main className="h-full w-full bg-white">
      <AutoIframe src={catalogue.story_guide_file} fixedHeight />
    </main>
  )
}

export default CourseStoryGuide
