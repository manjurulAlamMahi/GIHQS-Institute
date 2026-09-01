import { useState } from "react"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import { 
  useApplyForCertificationMutation,
  useGetCertificationCataloguesQuery
} from "@/features/certification/api/certificationApi"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { toast } from "sonner"
import { Loader2 } from "lucide-react"
import { useNavigate } from "react-router"

import { ApplicationField } from "./ApplicationField"
import { ApplicationSection } from "./ApplicationSection"
import { ROUTES } from "@/routes/routes.constants"

// Zod validation schema
const formSchema = z.object({
  first_name: z.string().min(2, "First name is required"),
  last_name: z.string().min(2, "Last name is required"),
  email: z.email("Invalid email address"),
  phone: z.string().min(5, "Phone number is required"),
  country: z.string().min(2, "Country is required"),
  city: z.string().min(2, "City is required"),
  current_job_title: z.string().min(2, "Job title is required"),
  organization: z.string().min(2, "Organization is required"),
  linkedin_profile: z.string().optional(),
  years_of_experience: z.string().min(1, "Required"),
  primary_area_of_experience: z.string().min(2, "Required"),
  professional_role: z.string().min(2, "Required"),
  certification_title: z.string().min(1, "Required"),
  confirm_accuracy: z.boolean().refine(val => val === true, "Required"),
  agree_policies: z.boolean().refine(val => val === true, "Required"),
})

type FormValues = z.infer<typeof formSchema>

export function CertificationApplicationForm() {
  const [apply, { isLoading }] = useApplyForCertificationMutation()
  const { data: cataloguesData, isLoading: isLoadingCatalogues } = useGetCertificationCataloguesQuery()
  const certifications = cataloguesData?.data?.certifications || []
  const [file, setFile] = useState<File | null>(null)
  const [fileError, setFileError] = useState("")
  const navigate = useNavigate()

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors },
  } = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      first_name: "",
      last_name: "",
      email: "",
      phone: "",
      country: "",
      city: "",
      current_job_title: "",
      organization: "",
      linkedin_profile: "",
      years_of_experience: "",
      primary_area_of_experience: "",
      professional_role: "",
      certification_title: "", // default selection
      confirm_accuracy: false,
      agree_policies: false,
    },
  })

  const onSubmit = async (data: FormValues) => {
    if (!file) {
      setFileError("Resume/CV is required")
      return
    }
    setFileError("")

    const formData = new FormData()
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined) {
        formData.append(key, typeof value === "boolean" ? (value ? "1" : "0") : (value as string))
      }
    })
    
    const selectedCert = certifications.find(c => c.id.toString() === data.certification_title)
    if (selectedCert) {
      formData.set("certification_title", selectedCert.title)
      formData.set("catalogue_id", selectedCert.id.toString())
    }
    formData.append("resume_cv", file)

    try {
      const res = await apply(formData).unwrap()
      toast.success(res.message || "Application submitted successfully!")
      navigate(ROUTES.DASHBOARD_CERTIFICATIONS)
    } catch (err: any) {
      toast.error(err?.data?.message || "Failed to submit application")
    }
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setFile(e.target.files[0])
      setFileError("")
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <ApplicationSection title="1. Applicant Information">
        <div className="grid gap-5 md:grid-cols-2">
          <ApplicationField
            label="First Name"
            placeholder="e.g. John"
            {...register("first_name")}
            error={errors.first_name?.message}
          />
          <ApplicationField
            label="Last Name"
            placeholder="e.g. Doe"
            {...register("last_name")}
            error={errors.last_name?.message}
          />
          <ApplicationField
            label="Email Address"
            type="email"
            placeholder="e.g. john@example.com"
            {...register("email")}
            error={errors.email?.message}
          />
          <ApplicationField
            label="Phone Number"
            placeholder="e.g. +1 234 567 8900"
            {...register("phone")}
            error={errors.phone?.message}
          />
          <ApplicationField
            label="Country"
            placeholder="e.g. United States"
            {...register("country")}
            error={errors.country?.message}
          />
          <ApplicationField
            label="City"
            placeholder="e.g. New York"
            {...register("city")}
            error={errors.city?.message}
          />
          <ApplicationField
            label="Current Job Title"
            placeholder="e.g. Quality Manager"
            {...register("current_job_title")}
            error={errors.current_job_title?.message}
          />
          <ApplicationField
            label="Organization / Employer"
            placeholder="e.g. ABC Healthcare"
            {...register("organization")}
            error={errors.organization?.message}
          />
          <div className="md:col-span-2">
            <ApplicationField
              label="LinkedIn Profile"
              placeholder="https://linkedin.com/in/johndoe"
              optional
              {...register("linkedin_profile")}
              error={errors.linkedin_profile?.message}
            />
          </div>
        </div>
      </ApplicationSection>

      <ApplicationSection title="2. Professional Background">
        <div className="grid gap-5 md:grid-cols-2">
          <label className="space-y-2 flex flex-col">
            <span className="text-[14px] font-semibold text-[#14392f]">
              Years of Professional Experience <span className="text-[#d4aa3a]">*</span>
            </span>
            <Select onValueChange={(val) => setValue("years_of_experience", val)}>
              <SelectTrigger className="h-11 rounded-[8px] border-border bg-white text-[15px] shadow-none focus:ring-[#14392f]/20">
                <SelectValue placeholder="Select years" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="0-2">0 - 2 years</SelectItem>
                <SelectItem value="3-5">3 - 5 years</SelectItem>
                <SelectItem value="5-10">5 - 10 years</SelectItem>
                <SelectItem value="10+">10+ years</SelectItem>
              </SelectContent>
            </Select>
            {errors.years_of_experience && <span className="text-xs text-red-500 mt-1">{errors.years_of_experience.message}</span>}
          </label>
          
          <ApplicationField
            label="Primary Area of Experience"
            placeholder="e.g. Healthcare Quality & Patient Safety"
            {...register("primary_area_of_experience")}
            error={errors.primary_area_of_experience?.message}
          />
          <ApplicationField
            label="Professional Role"
            placeholder="e.g. Education Department"
            {...register("professional_role")}
            error={errors.professional_role?.message}
          />
          <label className="space-y-2 flex flex-col">
            <span className="text-[14px] font-semibold text-[#14392f]">
              Upload Resume / CV <span className="text-[#d4aa3a]">*</span>
            </span>
            <input
              type="file"
              accept=".pdf,.doc,.docx"
              onChange={handleFileChange}
              className="block h-11 w-full rounded-[8px] border border-border bg-white px-3 py-2 text-[14px] text-muted-foreground file:mr-3 file:rounded-sm file:border file:border-border file:bg-white file:px-3 file:text-[14px] file:text-[#111827]"
            />
            {fileError && <span className="text-xs text-red-500 mt-1">{fileError}</span>}
          </label>
        </div>
      </ApplicationSection>

      <ApplicationSection title="3. Certification Selection">
        <label className="space-y-2 flex flex-col">
          <span className="text-[14px] font-semibold text-[#14392f]">
            Certification Applied For <span className="text-[#d4aa3a]">*</span>
          </span>
          <Select 
            value={watch("certification_title")} 
            onValueChange={(val) => setValue("certification_title", val)}
          >
            <SelectTrigger className="h-11 w-full rounded-[8px] border-border bg-white text-[15px] shadow-none focus:ring-[#14392f]/20">
              <SelectValue placeholder={isLoadingCatalogues ? "Loading..." : "Select certification"} />
            </SelectTrigger>
            <SelectContent>
              {certifications.map((cert) => (
                <SelectItem key={cert.id} value={cert.id.toString()}>
                  {cert.title}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          {errors.certification_title && <span className="text-xs text-red-500 mt-1">{errors.certification_title.message}</span>}
        </label>
      </ApplicationSection>

      <ApplicationSection title="4. Review & Submit">
        <div className="space-y-4">
          <label className="flex items-center gap-2 text-[15px] text-[#111827]">
            <input 
              type="checkbox" 
              className="size-4 rounded border-border"
              {...register("confirm_accuracy")}
            />
            I confirm that the information provided is accurate and complete.
          </label>
          {errors.confirm_accuracy && <span className="text-xs text-red-500 block">{errors.confirm_accuracy.message}</span>}

          <label className="flex items-center gap-2 text-[15px] text-[#111827]">
            <input 
              type="checkbox" 
              className="size-4 rounded border-border"
              {...register("agree_policies")}
            />
            I agree to follow the GIHQS certification policies, examination rules, and eligibility review requirements.
          </label>
          {errors.agree_policies && <span className="text-xs text-red-500 block">{errors.agree_policies.message}</span>}
        </div>
      </ApplicationSection>

      <div className="flex flex-col gap-4 pb-4 md:flex-row md:items-center md:justify-between">
        <p className="text-[14px] text-[#111827]">
          After submission, your application will be assessed through an eligibility workflow, depending on your profile.
        </p>
        <button 
          type="submit"
          disabled={isLoading}
          className="h-11 rounded-full flex items-center justify-center min-w-50 bg-[#14392f] px-7 text-[15px] font-medium text-white hover:bg-[#0f2f26] disabled:opacity-70 disabled:cursor-not-allowed"
        >
          {isLoading ? <Loader2 className="h-4 w-4 animate-spin mr-2" /> : null}
          {isLoading ? "Submitting..." : "Submit Application"}
        </button>
      </div>
    </form>
  )
}
