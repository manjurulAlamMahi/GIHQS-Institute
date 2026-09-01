import { Input } from "@/components/ui/input"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { forwardRef } from "react"

export type ApplicationFieldConfig = React.ComponentProps<"input"> & {
  label: string
  required?: boolean
  placeholder?: string
  type?: "input" | "select"
  options?: string[]
  inputType?: string
  error?: string
  value?: string
  onValueChange?: (val: string) => void
}

export function RequiredMark() {
  return <span className="text-[#C7A44D]">*</span>
}

export const ApplicationField = forwardRef<HTMLInputElement, ApplicationFieldConfig>(({
  label,
  required,
  placeholder,
  type = "input",
  options,
  inputType = "text",
  error,
  value,
  onValueChange,
  ...props
}, ref) => {
  return (
    <label className="space-y-2 flex flex-col">
      <span className="block text-sm font-bold text-[#0F2F26]">
        {label} {required && <RequiredMark />}
      </span>
      {type === "select" ? (
        <Select value={value} onValueChange={onValueChange}>
          <SelectTrigger className="h-11! w-full rounded-xl border-[#d7e1de] bg-white px-4 text-left text-sm text-[#59736b] shadow-none">
            <SelectValue placeholder={placeholder} />
          </SelectTrigger>
          <SelectContent>
            {options?.map((option) => (
              <SelectItem key={option} value={option}>
                {option}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      ) : (
        <Input
          ref={ref}
          type={inputType}
          placeholder={placeholder}
          className="h-11! rounded-xl border-[#d7e1de] bg-white px-4 text-sm shadow-none placeholder:text-[#7b8f89]"
          {...props}
        />
      )}
      {error && <span className="text-xs text-red-500 mt-1">{error}</span>}
    </label>
  )
})

ApplicationField.displayName = "ApplicationField"
export default ApplicationField
