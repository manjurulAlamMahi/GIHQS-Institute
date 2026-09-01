import { useState, useEffect } from "react";
import { useSearchParams } from "react-router";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

import { Search } from "lucide-react";

export default function CatalogueFilters() {
  const [searchParams, setSearchParams] = useSearchParams();
  
  const activeFilter = searchParams.get("filtering") || "";
  const typeFilter = searchParams.get("sorting") || "all";
  const keywordParam = searchParams.get("keyword") || "";

  const [localKeyword, setLocalKeyword] = useState(keywordParam);

  useEffect(() => {
    const timer = setTimeout(() => {
      setSearchParams((prev) => {
        if (localKeyword) prev.set("keyword", localKeyword);
        else prev.delete("keyword");
        return prev;
      }, { replace: true, preventScrollReset: true });
    }, 500);

    return () => clearTimeout(timer);
  }, [localKeyword, setSearchParams]);

  const handleFiltering = (filtering: "all" | "featured" | "trending" | "popular") => {
    setSearchParams((prev) => {
      if (filtering === "all" || prev.get("filtering") === filtering) {
        prev.delete("filtering");
      } else {
        prev.set("filtering", filtering);
      }
      
      prev.delete("sorting");
      return prev;
    }, { replace: true, preventScrollReset: true });
  };

  const handleSorting = (sorting: string) => {
    setSearchParams((prev) => {
      if (sorting !== "all") {
        prev.set("sorting", sorting);
      } else {
        prev.delete("sorting");
      }
      
      prev.delete("filtering");
      return prev;
    }, { replace: true, preventScrollReset: true });
  };

  return (
    <section className="w-full font-sans py-10">

      {/* Title & Static Notice Segment */}
      <div className="space-y-1.5 mb-6">
        <h2 className="text-2xl md:text-3xl font-serif text-[#0f3421] font-medium tracking-wide">
          Browse All Offerings
        </h2>
        <p className="text-xs md:text-sm text-neutral-500">
          Featured items should be selected dynamically from admin using a featured toggle.
        </p>
      </div>

      {/* Main Search and Dropdown Controls Bar */}
      <div className="w-full bg-[#f8faf9] border border-neutral-200/80 rounded-2xl p-4 flex flex-col sm:flex-row items-center gap-4 mb-6">
        {/* Search Field Input */}
        <div className="relative w-full sm:flex-1">
          <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" />
          <Input
            type="search"
            value={localKeyword}
            onChange={(e) => setLocalKeyword(e.target.value)}
            placeholder="Search certifications, courses, learning modules, or toolkits"
            className="w-full h-12 pl-10 pr-4 bg-white border-neutral-200 focus-visible:ring-[#113f27] text-sm text-neutral-700 placeholder:text-neutral-400 rounded-xl shadow-none"
          />
        </div>

        {/* Dropdown Type Select */}
        <div className="w-full sm:w-45">
          <Select value={typeFilter} onValueChange={handleSorting}>
            <SelectTrigger className="w-full h-12! bg-white border-neutral-200 focus:ring-[#113f27] text-neutral-700 font-medium rounded-xl text-sm shadow-none">
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
      </div>

      {/* Toggle Filter Pills Actions Container */}
      <div className="flex overflow-x-auto items-center gap-3 h-12">
        {/* All Filter Button */}
        <Button
          type="button"
          onClick={() => handleFiltering("all")}
          className={`h-11 px-12 rounded-sm font-medium text-sm border transition-all ${activeFilter === ""
              ? "bg-[#164d33] hover:bg-[#113c27] text-white border-transparent"
              : "bg-[#f8faf9] hover:bg-neutral-100 text-neutral-700 border-neutral-200"
            }`}
        >
          All
        </Button>

        {/* Featured Filter Button */}
        <Button
          type="button"
          onClick={() => handleFiltering("featured")}
          className={`h-11 px-12 rounded-sm font-medium text-sm border transition-all shadow-none ${activeFilter === "featured"
              ? "bg-[#164d33] hover:bg-[#113c27] text-white border-transparent"
              : "bg-[#f8faf9] hover:bg-neutral-100 text-neutral-700 border-neutral-200"
            }`}
        >
          Featured
        </Button>

        {/* Trending Filter Button */}
        <Button
          type="button"
          onClick={() => handleFiltering("trending")}
          className={`h-11 px-12 rounded-sm font-medium text-sm border transition-all shadow-none ${activeFilter === "trending"
              ? "bg-[#164d33] hover:bg-[#113c27] text-white border-transparent"
              : "bg-[#f8faf9] hover:bg-neutral-100 text-neutral-700 border-neutral-200"
            }`}
        >
          Trending
        </Button>

        {/* Popular Filter Button */}
        <Button
          type="button"
          onClick={() => handleFiltering("popular")}
          className={`h-11 px-12 rounded-sm font-medium text-sm border transition-all shadow-none ${activeFilter === "popular"
              ? "bg-[#164d33] hover:bg-[#113c27] text-white border-transparent"
              : "bg-[#f8faf9] hover:bg-neutral-100 text-neutral-700 border-neutral-200"
            }`}
        >
          Popular
        </Button>
      </div>

    </section>
  );
}