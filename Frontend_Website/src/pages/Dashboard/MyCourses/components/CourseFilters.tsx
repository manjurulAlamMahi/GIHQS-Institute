import { Search } from "lucide-react"
import { useSearchParams } from "react-router"
import { useState, useEffect } from "react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"

const tabs = [
  { label: "All", value: "all" },
  { label: "Certification", value: "Certification" },
  { label: "Course", value: "Course" },
  { label: "Webinar", value: "Webinar" },
  { label: "Module", value: "Module" },
  { label: "Toolkit", value: "Toolkit" },
] as const

export function CourseFilters() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [searchQuery, setSearchQuery] = useState(searchParams.get("keyword") || "")
  
  useEffect(() => {
    const timer = setTimeout(() => {
      setSearchParams(
        (prev) => {
          const params = new URLSearchParams(prev)
          const currentKeyword = params.get("keyword")
          const newKeyword = searchQuery.trim()

          if (newKeyword) {
            params.set("keyword", newKeyword)
          } else {
            params.delete("keyword")
          }

          if (currentKeyword !== params.get("keyword")) {
            return params
          }
          return prev
        },
        { preventScrollReset: true }
      )
    }, 400)
    
    return () => clearTimeout(timer)
  }, [searchQuery, setSearchParams])
  
  const handleTabClick = (value: string) => {
    setSearchParams(
      (prev) => {
        const params = new URLSearchParams(prev)
        if (value === "all") {
          params.delete("service_type")
        } else {
          params.set("service_type", value)
        }
        return params
      },
      { preventScrollReset: true }
    )
  }

  const activeTab = searchParams.get("service_type") || "all"

  return (
    <div className="space-y-4">
      <div className="flex flex-col gap-3 sm:flex-row">
        <div className="relative flex-1">
          <Search className="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            type="search"
            placeholder="Search my courses..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="h-10 rounded-full border-transparent bg-white pl-10 text-sm shadow-none focus-visible:ring-[#14392f]/20"
          />
        </div>
      </div>

      <div className="inline-flex flex-wrap items-center gap-1 rounded-full bg-white p-1 shadow-sm">
        {tabs.map((tab) => {
          const isActive = activeTab === tab.value

          return (
          <Button
            key={tab.value}
            type="button"
            onClick={() => handleTabClick(tab.value)}
            className={`h-8 rounded-full px-4 text-[15px] font-medium shadow-none ${
              isActive
                ? "bg-[#166046] text-white hover:bg-[#123f32]"
                : "bg-transparent text-[#111827] hover:bg-muted"
            }`}
          >
            {tab.label}
          </Button>
          )
        })}
      </div>
    </div>
  )
}
