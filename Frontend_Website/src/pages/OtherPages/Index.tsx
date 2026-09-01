import { useParams } from "react-router"
import { useGetOtherPageQuery } from "@/features/about/api/aboutApi"
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton"
import AutoIframe from "@/components/shared/AutoIframe"
import { useEffect } from "react"

import DOMPurify from "dompurify"

export default function OtherPage() {
  const { slug } = useParams<{ slug: string }>()
  const { data, isLoading, error } = useGetOtherPageQuery(slug as string, {
    skip: !slug,
  })

  // Scroll to top on slug change
  useEffect(() => {
    window.scrollTo(0, 0)
  }, [slug])

  if (isLoading) {
    return (
      <main className="bg-white min-h-screen">
        <DocumentSkeleton />
      </main>
    )
  }

  if (error || !data?.data?.other_page) {
    return (
      <main className="bg-[#F7FAF9] min-h-screen flex items-center justify-center">
        <div className="text-center space-y-4">
          <h2 className="text-2xl font-serif text-gray-800">Page not found</h2>
          <p className="text-gray-500">The requested page could not be loaded.</p>
        </div>
      </main>
    )
  }

  const pageData = data.data.other_page

  return (
    <main className="bg-white min-h-screen">
      {pageData.injected_status && pageData.content_file ? (
        <AutoIframe src={pageData.content_file} />
      ) : (
        <div className="container mx-auto py-16 px-4 md:px-8">
          <h1 className="text-3xl md:text-5xl font-serif mb-8 text-primary">
            {pageData.title}
          </h1>
          {pageData.content_file && (
            <div
              className="prose max-w-none prose-lg prose-headings:font-serif prose-a:text-primary"
              dangerouslySetInnerHTML={{ 
                __html: DOMPurify.sanitize(pageData.content_file) 
              }}
            />
          )}
        </div>
      )}
    </main>
  )
}
