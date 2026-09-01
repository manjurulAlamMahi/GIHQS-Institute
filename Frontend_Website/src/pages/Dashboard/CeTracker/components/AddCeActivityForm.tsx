import React from "react"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import {
  Select,   
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { toast } from "sonner"
import { useGetCertificationCataloguesQuery } from "@/features/catalogue/api/catalogueApi"
import { useAddCeActivityMutation } from "@/features/profile/api/profileApi"

const textFields = [
  { label: "Related Domain", placeholder: "Enter related domain", name: "domain" },
  { label: "Activity Type", placeholder: "Enter activity type", name: "activity_type" },
  { label: "Activity Title", placeholder: "Enter CE activity title", name: "activity_title" },
  { label: "Provider / Organization", placeholder: "Enter provider or organization name", name: "provider" },
  { label: "Completion Date", placeholder: "", name: "completion_date", type: "date" },
]

export function AddCeActivityForm() {
  const { data, isLoading } = useGetCertificationCataloguesQuery()
  const [addCeActivity, { isLoading: isSubmitting }] = useAddCeActivityMutation()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    const formElement = e.currentTarget
    const formData = new FormData(formElement)
    try {
      await addCeActivity(formData).unwrap()
      toast.success("CE Activity added successfully.")
      formElement.reset()
    } catch (err: any) {
      console.error(err)
      toast.error(err?.data?.message || "Failed to add CE Activity.")
    }
  }

  return (
    <section id="add-ce-activity" className="scroll-mt-6 rounded-[12px] bg-white p-6 shadow-sm">
      <div>
        <div className="flex flex-wrap items-start justify-between gap-3">
          <div>
            <h2 className="text-[20px] font-semibold text-[#111827]">
              Add CE Activity
            </h2>
            <p className="mt-1 text-[14px] text-blue-600">
              Compact submission form with certification and domain assignment.
            </p>
          </div>
          <button
            type="button"
            onClick={() => {
              const scrollContainer = document.querySelector<HTMLElement>("[data-dashboard-scroll]")
              scrollContainer?.scrollTo({ top: 0, left: 0, behavior: "smooth" })
            }}
            className="text-[14px] font-medium text-[#14392f] underline underline-offset-4 hover:text-[#0f2f26]"
          >
            Back to top
          </button>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="mt-8 space-y-5">
        <div className="grid gap-5 md:grid-cols-3">
          <label className="space-y-2 flex flex-col">
            <span className="text-[14px] font-medium text-[#111827]">Related Certification</span>
            <Select name="catalogue_id">
              <SelectTrigger className="h-10 w-full rounded-none border-[#d0d5dd] text-[15px] shadow-none focus-visible:ring-[#14392f]/20 bg-white">
                <SelectValue placeholder="Select certification" />
              </SelectTrigger>
              <SelectContent>
                {isLoading ? (
                  <SelectItem value="loading" disabled>Loading...</SelectItem>
                ) : (
                  data?.data?.certifications?.map((cert) => (
                    <SelectItem key={cert.id} value={cert.id.toString()}>
                      {cert.title}
                    </SelectItem>
                  ))
                )}
              </SelectContent>
            </Select>
          </label>
          {textFields.map((field) => (
            <label key={field.name} className="space-y-2">
              <span className="text-[14px] font-medium text-[#111827]">{field.label}</span>
              <Input
                name={field.name}
                type={field.type || "text"}
                placeholder={field.placeholder}
                className="h-10 rounded-none border-[#d0d5dd] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
            </label>
          ))}
        </div>

        <div className="grid gap-5 md:grid-cols-[1fr_1fr]">
          <label className="space-y-2">
            <span className="text-[14px] font-medium text-[#111827]">CE Credits Earned</span>
            <Input
              name="credits_earned"
              type="number"
              step="0.5"
              placeholder="Enter credits"
              className="h-10 rounded-none border-[#d0d5dd] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
            />
          </label>

          <label className="space-y-2">
            <span className="text-[14px] font-medium text-[#111827]">Supporting Evidence</span>
            <div className="flex h-10 items-center gap-3">
              <input
                name="evidence_file"
                type="file"
                className="block text-[14px] text-[#667085] file:mr-3 file:h-9 file:rounded-none file:border file:border-[#d0d5dd] file:bg-white file:px-4 file:text-[14px] file:font-medium file:text-[#111827]"
              />
            </div>
          </label>
        </div>

        <label className="block space-y-2">
          <span className="text-[14px] font-medium text-[#111827]">Description / Notes</span>
          <Textarea
            name="description"
            placeholder="Briefly describe the activity, learning outcomes, or relevance to the selected certification/domain."
            className="min-h-27 rounded-[12px] border-[#d0d5dd] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
          />
        </label>

        <p className="text-[12px] text-[#667085]">
          Upload certificates of completion, attendance proof, transcript, agenda, or other supporting documents for admin review.
        </p>

        <div className="flex flex-wrap gap-3">
          <button
            type="submit"
            disabled={isSubmitting}
            className="h-10 rounded-full bg-[#14392f] px-6 text-[15px] font-medium text-white hover:bg-[#0f2f26] disabled:opacity-50"
          >
            {isSubmitting ? "Submitting..." : "Submit CE Activity"}
          </button>
          <button
            type="reset"
            disabled={isSubmitting}
            className="h-10 rounded-full border border-[#14392f] bg-white px-6 text-[15px] font-medium text-[#14392f] hover:bg-muted"
          >
            Clear Form
          </button>
        </div>
      </form>
    </section>
  )
}
