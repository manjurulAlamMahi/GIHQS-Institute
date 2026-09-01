import { Button } from "@/components/ui/button";
import { Link } from "react-router";

export default function AccreditationCTA() {
  return (
    <section className="container mx-auto px-4 py-10 md:py-15 sm:px-6 lg:px-8">
      <div
        className="rounded-3xl px-6 py-14 text-center md:px-10"
        style={{
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%), #FFF",
        }}
      >
        <h2 className="mx-auto max-w-5xl font-serif text-3xl font-medium leading-tight text-white md:text-4xl">
          Begin The Accreditation Process By Completing The{" "}
          <span className="italic text-[#D4AA3A]">
            Pre-Application Registration.
          </span>
        </h2>

        <Button
          asChild
          className="mt-8 h-12 rounded-full bg-[#F4C84E] px-8 text-sm font-bold text-[#102D25] hover:bg-[#EABF45]"
        >
          <Link to="/accreditation/apply">Apply for Accreditation</Link>
        </Button>
      </div>
    </section>
  );
}
