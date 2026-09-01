import { Search } from "lucide-react"

import { Input } from "@/components/ui/input"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

type Props = {
  search: string
  status: string
  onSearchChange: (value: string) => void
  onStatusChange: (value: string) => void
}

export function CertificationToolbar({ search, status, onSearchChange, onStatusChange }: Props) {
  return (
    <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
      <div>
        <h2 className="text-[20px] font-semibold leading-none text-[#14392f]">
          My Certifications
        </h2>
        <p className="mt-2 text-[14px] text-muted-foreground">
          Your certifications and progress
        </p>
      </div>

      <div className="flex flex-col gap-3 sm:flex-row">
        <div className="relative w-full sm:w-[230px]">
          <Search className="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            type="search"
            placeholder="Search..."
            value={search}
            onChange={(event) => onSearchChange(event.target.value)}
            className="h-10 rounded-[8px] border-transparent bg-[#f2f2f4] pl-10 text-sm shadow-none focus-visible:ring-[#14392f]/20"
          />
        </div>

        <div className="flex items-center gap-2">
          <span className="text-sm text-muted-foreground">Filter:</span>
          <Select value={status} onValueChange={onStatusChange}>
            <SelectTrigger className="h-10! w-full rounded-[8px] border-transparent bg-[#f2f2f4] text-sm font-semibold shadow-none focus:ring-[#14392f]/20 sm:w-[145px]">
              <SelectValue placeholder="All status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All status</SelectItem>
              <SelectItem value="in-progress">In progress</SelectItem>
              <SelectItem value="exam-scheduled">Exam scheduled</SelectItem>
              <SelectItem value="renewal-due">Renewal due</SelectItem>
              <SelectItem value="active">Active</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>
    </div>
  )
}
