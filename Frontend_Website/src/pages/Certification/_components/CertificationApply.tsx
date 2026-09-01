import { Link } from "react-router"
import { Button } from "@/components/ui/button"
import { ROUTES } from "@/routes/routes.constants"

export default function CertificationApply() {
  return (
    <section
      style={{
        borderRadius: "24px",
        background:
          "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #FFF",
      }}
      className="my-10 w-full p-8 text-center font-sans text-white shadow-xl md:p-14"
    >
      <div className="mx-auto max-w-4xl space-y-6">
        <h2 className="font-serif text-3xl font-normal tracking-tight md:text-4xl">
          Ready To Apply For <br />
          <span className="font-normal text-[#F0D070] italic">
            AIHQSP Certification?
          </span>
        </h2>

        <div className="pt-2">
          <Link to={ROUTES.APPLY_CERTIFICATION}>
            <Button
              type="button"
              style={{ backgroundColor: "#F0D070" }}
              className="h-11 rounded-full border-none px-7 text-xs font-bold tracking-wide text-black shadow-none transition-all duration-200 hover:scale-[1.02] hover:bg-[#ebd48a]"
            >
              Apply for AIHQSP Certification
            </Button>
          </Link>
        </div>
      </div>
    </section>
  )
}
