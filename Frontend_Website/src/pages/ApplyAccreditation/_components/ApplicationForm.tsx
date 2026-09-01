import { useState } from "react"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import { Button } from "@/components/ui/button"
import { Textarea } from "@/components/ui/textarea"
import { Link, useNavigate } from "react-router"
import { toast } from "sonner"
import { Loader2 } from "lucide-react"

import ApplicationField, { RequiredMark } from "./ApplicationField"
import ApplicationFormSection from "./ApplicationFormSection"
import AttachmentCard from "./AttachmentCard"
import { useApplyAccreditationMutation } from "@/features/accreditation/api/accreditationApi"
import { ROUTES } from "@/routes/routes.constants"

// Zod validation schema
const formSchema = z.object({
  applicant_category: z.string().min(1, "Applicant category is required"),
  applicant_name: z.string().min(2, "Applicant name is required"),
  department_division: z.string().optional(),
  country: z.string().min(1, "Country is required"),
  city: z.string().min(2, "City is required"),
  website_url: z.string().url("Must be a valid URL").or(z.literal("")).optional(),
  year_established: z.string().optional(),
  program_name: z.string().min(2, "Program name is required"),
  program_type: z.string().min(1, "Program type is required"),
  program_delivery_format: z.string().min(1, "Delivery format is required"),
  estimated_annual_participants: z.string().optional(),
  primary_language_of_instruction: z.string().optional(),
  program_launch_date: z.string().optional(),
  primary_contact_person: z.string().min(2, "Primary contact is required"),
  contact_title_position: z.string().min(2, "Contact title is required"),
  email_address: z.string().email("Invalid email address"),
  phone_number: z.string().min(5, "Phone number is required"),
  additional_information: z.string().optional(),
})

type FormValues = z.infer<typeof formSchema>

export default function ApplicationForm() {
  const [apply, { isLoading }] = useApplyAccreditationMutation()
  const navigate = useNavigate()

  const [overviewDoc, setOverviewDoc] = useState<File | null>(null)
  const [governanceDoc, setGovernanceDoc] = useState<File | null>(null)

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors },
  } = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      applicant_category: "",
      applicant_name: "",
      department_division: "",
      country: "",
      city: "",
      website_url: "",
      year_established: "",
      program_name: "",
      program_type: "",
      program_delivery_format: "",
      estimated_annual_participants: "",
      primary_language_of_instruction: "",
      program_launch_date: "",
      primary_contact_person: "",
      contact_title_position: "",
      email_address: "",
      phone_number: "",
      additional_information: "",
    },
  })

  const onSubmit = async (data: FormValues) => {
    const formData = new FormData()
    
    // Append all text fields
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })
    
    // Append files if they exist
    if (overviewDoc) {
      formData.append("program_overview_doc", overviewDoc)
    }
    if (governanceDoc) {
      formData.append("governance_policy_doc", governanceDoc)
    }

    try {
      const res = await apply(formData).unwrap()
      toast.success(res.message || "Accreditation application submitted successfully.")
      navigate(ROUTES.DASHBOARD_ACCREDITATION)
    } catch (err: any) {
      toast.error(err?.data?.message || "Failed to submit application")
    }
  }

  const handleOverviewChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setOverviewDoc(e.target.files[0])
    }
  }

  const handleGovernanceChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setGovernanceDoc(e.target.files[0])
    }
  }

  return (
    <form 
      onSubmit={handleSubmit(onSubmit)}
      className="overflow-hidden rounded-[26px] bg-white shadow-[0_18px_48px_rgba(15,47,38,0.08)]"
    >
      <div className="border-b border-[#dfe8e5] px-6 py-8 md:px-8">
        <h2 className="font-serif text-3xl font-medium text-[#0F2F26] md:text-4xl">
          Application Form
        </h2>
        <p className="mt-2 text-sm text-[#5d756f]">
          Complete the fields below to begin your GIHQS accreditation
          application. Fields marked with an asterisk are required.
        </p>
      </div>

      <div className="space-y-6 p-6 md:p-8">
        <ApplicationFormSection
          number="1"
          title="Applicant Information"
          description="Identify the applicant and the entity or individual seeking accreditation."
        >
          <div className="grid gap-5 md:grid-cols-2">
            <ApplicationField 
              label="Applicant Category" 
              required 
              type="select" 
              placeholder="Select applicant category"
              options={[
                "Independent trainer", "Training company", "Academic institution",
                "Healthcare organization", "Professional association", "Certification body"
              ]}
              value={watch("applicant_category")}
              onValueChange={(val) => setValue("applicant_category", val)}
              error={errors.applicant_category?.message}
            />
            <ApplicationField 
              label="Applicant Name" 
              required 
              placeholder="Organization, provider, or personal/professional name"
              {...register("applicant_name")}
              error={errors.applicant_name?.message}
            />
            <ApplicationField 
              label="Department / Division (if applicable)" 
              placeholder="Example: Education Department or Quality & Patient Safety Division"
              {...register("department_division")}
              error={errors.department_division?.message}
            />
            <ApplicationField 
              label="Country" 
              required 
              placeholder="Example: Bangladesh"
              {...register("country")}
              error={errors.country?.message}
            />
            <ApplicationField 
              label="City" 
              required 
              placeholder="Example: Riyadh"
              {...register("city")}
              error={errors.city?.message}
            />
            <ApplicationField 
              label="Website URL" 
              placeholder="https://www.example.org"
              {...register("website_url")}
              error={errors.website_url?.message}
            />
            <ApplicationField 
              label="Year Established" 
              placeholder="Example: 2018"
              {...register("year_established")}
              error={errors.year_established?.message}
            />
          </div>
        </ApplicationFormSection>

        <ApplicationFormSection
          number="2"
          title="Program Information"
          description="Provide details about the specific program seeking accreditation."
        >
          <div className="grid gap-5 md:grid-cols-2">
            <ApplicationField 
              label="Program Name Seeking Accreditation" 
              required 
              placeholder="Example: Lean Healthcare Quality Certificate Program"
              {...register("program_name")}
              error={errors.program_name?.message}
            />
            <ApplicationField 
              label="Program Type" 
              required 
              type="select" 
              placeholder="Select program type"
              options={[
                "Certificate program", "Training course", "Diploma",
                "Workshop series", "Continuing education program", "Other"
              ]}
              value={watch("program_type")}
              onValueChange={(val) => setValue("program_type", val)}
              error={errors.program_type?.message}
            />
            <ApplicationField 
              label="Program Delivery Format" 
              required 
              type="select" 
              placeholder="Select delivery format"
              options={["In person", "Online", "Hybrid", "Blended learning"]}
              value={watch("program_delivery_format")}
              onValueChange={(val) => setValue("program_delivery_format", val)}
              error={errors.program_delivery_format?.message}
            />
            <ApplicationField 
              label="Estimated Annual Participants" 
              type="select" 
              placeholder="Select estimated volume"
              options={["1-25", "26-100", "101-250", "251-500", "500+"]}
              value={watch("estimated_annual_participants")}
              onValueChange={(val) => setValue("estimated_annual_participants", val)}
              error={errors.estimated_annual_participants?.message}
            />
            <ApplicationField 
              label="Primary Language of Instruction" 
              placeholder="Example: English"
              {...register("primary_language_of_instruction")}
              error={errors.primary_language_of_instruction?.message}
            />
            <ApplicationField 
              label="Program Launch Date (Month / Year)" 
              placeholder="MM / YYYY"
              {...register("program_launch_date")}
              error={errors.program_launch_date?.message}
            />
          </div>
        </ApplicationFormSection>

        <ApplicationFormSection
          number="3"
          title="Primary Contact Information"
          description="Identify the representative authorized to communicate with GIHQS regarding this application."
        >
          <div className="grid gap-5 md:grid-cols-2">
            <ApplicationField 
              label="Primary Contact Person" 
              required 
              placeholder="Example: Dr. Michael Carter"
              {...register("primary_contact_person")}
              error={errors.primary_contact_person?.message}
            />
            <ApplicationField 
              label="Contact Title / Position" 
              required 
              placeholder="Example: Program Director"
              {...register("contact_title_position")}
              error={errors.contact_title_position?.message}
            />
            <ApplicationField 
              label="Email Address" 
              required 
              inputType="email" 
              placeholder="name@example.org"
              {...register("email_address")}
              error={errors.email_address?.message}
            />
            <ApplicationField 
              label="Phone Number" 
              inputType="tel" 
              placeholder="Example: +1 (602) 555-0123"
              {...register("phone_number")}
              error={errors.phone_number?.message}
            />
          </div>
        </ApplicationFormSection>

        <ApplicationFormSection
          number="4"
          title="Supporting Attachments"
          description="Attach materials that help validate the applicant and support the initial program review. PDF preferred."
        >
          <div className="grid gap-5 md:grid-cols-2 items-stretch">
            <AttachmentCard
              title="Program Overview / Curriculum Document"
              help="Example: program brochure, curriculum outline, syllabus, or course structure document."
              onChange={handleOverviewChange}
              fileName={overviewDoc?.name}
            />
            <AttachmentCard
              title="Governance, Policy, or Supporting Institutional Document"
              help="Example: governance policy, organizational overview, quality statement, or related supporting documentation."
              onChange={handleGovernanceChange}
              fileName={governanceDoc?.name}
            />
          </div>
        </ApplicationFormSection>

        <ApplicationFormSection
          number="5"
          title="Additional Information"
          description="Share any information that may help GIHQS understand the scope, audience, or intent of the program."
        >
          <label className="space-y-2 flex flex-col">
            <span className="block text-sm font-bold text-[#0F2F26]">
              Additional Information About the Program
            </span>
            <Textarea
              placeholder="You may describe the target audience, learning objectives, healthcare focus area, prior approvals, delivery context, or anything else that supports the application."
              className="min-h-32 rounded-xl border-[#d7e1de] bg-white px-4 py-4 text-sm shadow-none placeholder:text-[#7b8f89]"
              {...register("additional_information")}
            />
            {errors.additional_information && <span className="text-xs text-red-500 mt-1">{errors.additional_information.message}</span>}
          </label>
        </ApplicationFormSection>

        <div className="border-t border-[#dfe8e5] pt-6">
          <div className="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <p className="max-w-3xl text-sm leading-6 text-[#5d756f]">
              Fields marked with <RequiredMark /> are required. Submitting this
              form initiates the accreditation application review process. GIHQS
              will review the information provided and contact the applicant
              regarding next steps, documentation review, and applicable fees.
            </p>
            <div className="flex justify-end gap-3 shrink-0">
              <Button
                type="button"
                asChild
                variant="outline"
                className="h-11 rounded-full border-[#d7e1de] bg-white px-7 text-sm font-semibold text-[#0F2F26]"
              >
                <Link to="/accreditation">Back</Link>
              </Button>
              <Button 
                type="submit" 
                disabled={isLoading}
                className="h-11 rounded-full min-w-50 bg-[#006045] px-7 text-sm font-bold text-white hover:bg-[#0F2F26] disabled:opacity-70 disabled:cursor-not-allowed"
              >
                {isLoading ? (
                  <><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Submitting...</>
                ) : (
                  "Submit Accreditation Application"
                )}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </form>
  )
}
