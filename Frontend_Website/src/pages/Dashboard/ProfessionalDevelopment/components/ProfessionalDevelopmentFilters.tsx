import { useState, useEffect } from "react"
import { useSearchParams } from "react-router"
import { Search } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

export function ProfessionalDevelopmentFilters() {
  const [searchParams, setSearchParams] = useSearchParams()
  
  const activeFilter = searchParams.get("filtering") || ""
  const typeFilter = searchParams.get("sorting") || "all"
  const keywordParam = searchParams.get("keyword") || ""

  const [localKeyword, setLocalKeyword] = useState(keywordParam)

  useEffect(() => {
    const timer = setTimeout(() => {
      setSearchParams((prev) => {
        if (localKeyword) prev.set("keyword", localKeyword)
        else prev.delete("keyword")
        return prev
      }, { replace: true, preventScrollReset: true })
    }, 500)

    return () => clearTimeout(timer)
  }, [localKeyword, setSearchParams])

  const handleFiltering = (filtering: string) => {
    setSearchParams((prev) => {
      if (filtering === "all" || prev.get("filtering") === filtering) {
        prev.delete("filtering")
      } else {
        prev.set("filtering", filtering)
      }
      
      prev.delete("sorting")
      prev.delete("catalogue_id")
      return prev
    }, { replace: true, preventScrollReset: true })
  }

  const handleSorting = (sorting: string) => {
    setSearchParams((prev) => {
      if (sorting !== "all") {
        prev.set("sorting", sorting)
      } else {
        prev.delete("sorting")
      }
      
      prev.delete("filtering")
      prev.delete("catalogue_id")
      return prev
    }, { replace: true, preventScrollReset: true })
  }

  const filters = [
    {
      label: "All",
      value: "all",
      isActive: activeFilter === "",
    },
    {
      label: "Featured",
      value: "featured",
      isActive: activeFilter === "featured",
    },
    {
      label: "Trending",
      value: "trending",
      isActive: activeFilter === "trending",
    },
    {
      label: "Popular",
      value: "popular",
      isActive: activeFilter === "popular",
    },
  ]

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 rounded-[18px] border border-border bg-white p-4 shadow-sm sm:flex-row">
        <div className="relative flex-1">
          <Search className="absolute left-4 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            type="search"
            value={localKeyword}
            onChange={(e) => setLocalKeyword(e.target.value)}
            placeholder="Search certifications, courses, learning modules, or toolkits"
            className="h-12 rounded-xl border-border bg-white pl-11 text-sm shadow-none focus-visible:ring-[#14392f]/20"
          />
        </div>

        <Select value={typeFilter} onValueChange={handleSorting}>
          <SelectTrigger className="h-12! w-full rounded-xl border-border bg-white text-sm shadow-none focus:ring-[#14392f]/20 sm:w-[210px]">
            <SelectValue placeholder="All Types" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All Types</SelectItem>
            <SelectItem value="certification">Certifications</SelectItem>
            <SelectItem value="course">Courses</SelectItem>
            <SelectItem value="module">Learning Module</SelectItem>
            <SelectItem value="toolkit">Toolkits</SelectItem>
            <SelectItem value="webinar">Webinars</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div className="flex overflow-x-auto items-center gap-3">
        {filters.map((filter) => (
          <Button
            key={filter.label}
            type="button"
            onClick={() => handleFiltering(filter.value)}
            className={`h-11 px-6 sm:px-10 rounded-xl border text-sm font-medium shadow-none shrink-0 ${
              filter.isActive
                ? "border-transparent bg-[#166046] text-white hover:bg-[#123f32]"
                : "border-border bg-white text-[#14392f] hover:bg-muted"
            }`}
          >
            {filter.label}
          </Button>
        ))}
      </div>
    </div>
  )
}
