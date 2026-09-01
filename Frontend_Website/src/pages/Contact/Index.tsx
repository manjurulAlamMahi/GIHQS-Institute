import { Skeleton } from "@/components/ui/skeleton"
import {
  useGetAboutContactQuery,
  useSubmitContactMessageMutation,
} from "@/features/about/api/aboutApi"
import { Loader2, Send } from "lucide-react"
import { toast } from "sonner"
const inputClasses =
  "h-10 rounded-md border border-[#D8E5E1] bg-white px-3 text-sm text-[#102B24] outline-none transition focus:border-[#C39A31] focus:ring-2 focus:ring-[#C39A31]/20"

const labelClasses = "text-sm font-medium text-[#102B24]"

export default function ContactPage() {
  const { data, isLoading } = useGetAboutContactQuery()
  const contactData = data?.data?.about_contact
  const [submitMessage, { isLoading: isSubmitting }] =
    useSubmitContactMessageMutation()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    const form = e.currentTarget
    const formData = new FormData(form)

    // The form payload is slightly different from names in inputs, so we map them correctly
    const payload = {
      first_name: formData.get("firstName") as string,
      last_name: formData.get("lastName") as string,
      email: formData.get("email") as string,
      phone: `${formData.get("countryCode") || ""}${formData.get("phoneNumber") || ""}`,
      organization: (formData.get("organization") as string) || "",
      service_of_interest: formData.get("serviceInterest") as string,
      message: formData.get("message") as string,
    }

    if (
      !payload.first_name ||
      !payload.last_name ||
      !payload.email ||
      !payload.message
    ) {
      toast.error("Please fill in all required fields.")
      return
    }

    try {
      const res = await submitMessage(payload).unwrap()
      if (res.success || (res as any).code === 201) {
        toast.success(res.message || "Contact message submitted successfully.")
        form.reset() // Reset form fields
      } else {
        toast.error(res.message || "Failed to submit message.")
      }
    } catch (err: any) {
      console.error("Submit Message Error:", err)

      // If it's a parsing error but status is 2xx, it might have actually succeeded
      if (
        err?.status === "PARSING_ERROR" &&
        err?.originalStatus >= 200 &&
        err?.originalStatus < 300
      ) {
        toast.success("Contact message submitted successfully.")
        form.reset()
        return
      }

      toast.error(
        err?.data?.message ||
          err?.error ||
          err?.message ||
          "An error occurred while submitting."
      )
    }
  }

  return (
    <main className="container mx-auto bg-[#F7FAF9] px-4 py-0 sm:px-6 md:py-8 lg:px-8">
      <section className="grid items-stretch gap-10 lg:grid-cols-[1fr_0.95fr]">
        <div className="h-full py-3">
          <h1 className="font-serif text-4xl leading-tight font-semibold text-[#102B24] sm:text-[2.7rem]">
            Contact GIHQS
          </h1>
          <p className="mt-6 max-w-xl text-xl leading-snug font-bold text-[#1E2825]">
            Have questions about GIHQS certifications, education programs, or
            the accreditation of education and training programs?
          </p>
          <p className="mt-7 max-w-2xl text-sm leading-6 text-[#48655D]">
            We welcome inquiries from healthcare professionals, educators, and
            organizations committed to advancing healthcare quality, patient
            safety, and high-reliability healthcare systems.
          </p>

          <form className="mt-8 max-w-2xl space-y-5" onSubmit={handleSubmit}>
            <div className="grid gap-3 sm:grid-cols-2">
              <div className="grid gap-2">
                <label className={labelClasses} htmlFor="first-name">
                  First Name *
                </label>
                <input
                  id="first-name"
                  name="firstName"
                  className={inputClasses}
                  required
                />
              </div>
              <div className="grid gap-2">
                <label className={labelClasses} htmlFor="last-name">
                  Last Name *
                </label>
                <input
                  id="last-name"
                  name="lastName"
                  className={inputClasses}
                  required
                />
              </div>
            </div>

            <div className="grid gap-2">
              <label className={labelClasses} htmlFor="email">
                Email Address *
              </label>
              <input
                id="email"
                name="email"
                type="email"
                className={inputClasses}
                required
              />
            </div>

            <div className="grid gap-2">
              <label className={labelClasses} htmlFor="phone-number">
                Phone Number
              </label>
              <div className="grid gap-3 sm:grid-cols-[0.8fr_1.6fr]">
                <input
                  aria-label="Country Code"
                  className={inputClasses}
                  name="countryCode"
                  placeholder="Country Code"
                />
                <input
                  id="phone-number"
                  className={inputClasses}
                  name="phoneNumber"
                  placeholder="Phone number"
                  type="tel"
                />
              </div>
            </div>

            <div className="grid gap-2">
              <label className={labelClasses} htmlFor="organization">
                Organization (Optional)
              </label>
              <input
                id="organization"
                name="organization"
                className={inputClasses}
              />
            </div>

            <div className="grid gap-2">
              <label className={labelClasses} htmlFor="service-interest">
                Service of Interest
              </label>
              <select
                id="service-interest"
                name="serviceInterest"
                className={`${inputClasses} appearance-none`}
                defaultValue="Professional Certification Programs"
              >
                <option value="Professional Certification Programs">
                  Professional Certification Programs
                </option>
                <option value="Education Programs">Education Programs</option>
                <option value="Accreditation of Education and Training Programs">
                  Accreditation of Education and Training Programs
                </option>
                <option value="Institutional Collaboration">
                  Institutional Collaboration
                </option>
                <option value="General Inquiry">General Inquiry</option>
              </select>
            </div>

            <div className="grid gap-2">
              <label className={labelClasses} htmlFor="message">
                Message *
              </label>
              <textarea
                id="message"
                name="message"
                required
                className="min-h-32 rounded-md border border-[#D8E5E1] bg-white px-3 py-3 text-sm text-[#102B24] transition outline-none placeholder:text-[#80938D] focus:border-[#C39A31] focus:ring-2 focus:ring-[#C39A31]/20"
                placeholder="Please provide details about your inquiry."
              />
            </div>

            <div className="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
              <p className="text-xs leading-5 text-[#61756D]">
                Our team typically responds within 1-2 business days.
              </p>
              <button
                className="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-[#176B56] px-7 text-sm font-semibold text-white shadow-[0_10px_22px_rgba(23,107,86,0.22)] transition hover:bg-[#125844] disabled:cursor-not-allowed disabled:opacity-70"
                type="submit"
                disabled={isSubmitting}
              >
                {isSubmitting ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" />
                    Sending...
                  </>
                ) : (
                  <>
                    <Send className="h-4 w-4" />
                    Send Message
                  </>
                )}
              </button>
            </div>
          </form>
        </div>

        {isLoading ? (
          <aside
            className="h-full rounded-[24px] px-8 py-10 text-white shadow-[0_18px_42px_rgba(15,47,38,0.14)] sm:px-12 lg:min-h-172.5"
            style={{
              background:
                "radial-gradient(79.7% 88.45% at 47.59% -5.47%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #0F2F26",
            }}
          >
            <Skeleton className="h-9 w-64 bg-white/10" />

            <div className="mt-9 space-y-7">
              {[1, 2].map((i) => (
                <div key={i} className="space-y-2">
                  <Skeleton className="h-5 w-16 bg-[#D4AA3A]/20" />
                  <Skeleton className="h-5 w-40 bg-white/10" />
                </div>
              ))}
              <div className="space-y-2">
                <Skeleton className="h-5 w-32 bg-[#D4AA3A]/20" />
                <Skeleton className="h-5 w-48 bg-white/10" />
                <Skeleton className="h-5 w-40 bg-white/10" />
                <Skeleton className="h-5 w-24 bg-white/10" />
              </div>
              <div className="space-y-2">
                <Skeleton className="h-5 w-24 bg-[#D4AA3A]/20" />
                <Skeleton className="h-5 w-32 bg-white/10" />
                <Skeleton className="h-5 w-48 bg-white/10" />
              </div>
            </div>

            <div className="mt-9 space-y-3 border-t border-white/16 pt-9">
              <Skeleton className="h-5 w-32 bg-[#D4AA3A]/20" />
              <div className="space-y-2">
                <Skeleton className="h-5 w-full max-w-xl bg-white/10" />
                <Skeleton className="h-5 w-full max-w-xl bg-white/10" />
                <Skeleton className="h-5 w-3/4 max-w-xl bg-white/10" />
              </div>
            </div>
          </aside>
        ) : contactData ? (
          <aside
            className="h-full rounded-[24px] px-8 py-10 text-white shadow-[0_18px_42px_rgba(15,47,38,0.14)] sm:px-12 lg:min-h-172.5"
            style={{
              background:
                "radial-gradient(79.7% 88.45% at 47.59% -5.47%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #0F2F26",
            }}
          >
            <h2 className="text-3xl leading-tight font-bold">
              {contactData.title}
            </h2>

            <div className="mt-9 space-y-7 text-sm leading-6 text-[#E2EEE9]">
              <div>
                <h3 className="font-bold text-[#D4AA3A]">Phone</h3>
                <p>{contactData.phone}</p>
              </div>

              <div>
                <h3 className="font-bold text-[#D4AA3A]">Email</h3>
                <p>{contactData.email}</p>
              </div>

              <div>
                <h3 className="font-bold text-[#D4AA3A]">Headquarters</h3>
                {contactData.address.split(/\r?\n/).map((line, idx) => (
                  <p key={idx}>{line}</p>
                ))}
              </div>

              <div>
                <h3 className="font-bold text-[#D4AA3A]">Working Hours</h3>
                <div
                  dangerouslySetInnerHTML={{
                    __html: contactData.working_hours,
                  }}
                />
              </div>
            </div>

            <div className="mt-9 border-t border-white/16 pt-9">
              <h3 className="font-bold text-[#D4AA3A]">GIHQS Mission</h3>
              <p className="mt-2 max-w-xl text-sm leading-6 text-[#E2EEE9]">
                {contactData.mission}
              </p>
            </div>
          </aside>
        ) : (
          <aside
            className="flex h-full items-center justify-center rounded-[24px] px-8 py-10 text-white shadow-[0_18px_42px_rgba(15,47,38,0.14)] sm:px-12 lg:min-h-172.5"
            style={{
              background:
                "radial-gradient(79.7% 88.45% at 47.59% -5.47%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #0F2F26",
            }}
          >
            <p className="text-[#E2EEE9]">
              Failed to load contact information.
            </p>
          </aside>
        )}
      </section>
    </main>
  )
}
