import { Input } from "@/components/ui/input"
import { forwardRef } from "react"

type ApplicationFieldProps = React.ComponentProps<"input"> & {
  label: string
  optional?: boolean
  error?: string
}

export const ApplicationField = forwardRef<HTMLInputElement, ApplicationFieldProps>(
  ({ label, optional = false, error, className, ...props }, ref) => {
    return (
      <label className="space-y-2 flex flex-col">
        <span className="text-[14px] font-semibold text-[#14392f]">
          {label} {!optional && <span className="text-[#d4aa3a]">*</span>}
          {optional && <span className="font-normal text-muted-foreground"> (Optional)</span>}
        </span>
        <Input
          ref={ref}
          className={`h-11 rounded-[8px] border-border bg-white text-[15px] shadow-none focus-visible:ring-[#14392f]/20 ${className || ""}`}
          {...props}
        />
        {error && <span className="text-xs text-red-500 mt-1">{error}</span>}
      </label>
    )
  }
)
ApplicationField.displayName = "ApplicationField"
