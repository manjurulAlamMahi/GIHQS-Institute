import type { Catalogue } from "@/types/catalogue.types";
import AutoIframe from "@/components/shared/AutoIframe";

interface Props {
  catalogue: Catalogue;
}

export default function CourseOverview({ catalogue }: Props) {
  if (!catalogue.details_file) {
    return (
      <section className="w-full py-12 font-sans text-center">
        <p className="text-neutral-500">No details available.</p>
      </section>
    );
  }

  return (
    <section className="bg-white h-full w-full">
      <AutoIframe src={catalogue.details_file} fixedHeight />
    </section>
  );
}