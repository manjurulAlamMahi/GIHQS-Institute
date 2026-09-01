import AboutFocusAreas from "./components/AboutFocusAreas";
import AboutInstituteHero from "./components/AboutInstituteHero";
import { useGetAboutInstituteQuery } from "@/features/about/api/aboutApi";
import { UniversalPageSkeleton } from "@/components/shared/UniversalPageSkeleton";
import AutoIframe from "@/components/shared/AutoIframe";

const AboutInstitute = () => {
  const { data, isLoading, error } = useGetAboutInstituteQuery();

  if (isLoading) {
    return (
      <main className="bg-[#F7FAF9] min-h-screen">
        <UniversalPageSkeleton />
      </main>
    );
  }

  if (error || !data?.data?.about_institutes) {
    return (
      <main className="bg-[#F7FAF9] min-h-screen flex items-center justify-center">
        <p className="text-gray-500">Failed to load about institute data.</p>
      </main>
    );
  }

  const aboutData = data.data.about_institutes;

  if (aboutData.injected_status && aboutData.content_file) {
    return (
      <main className="bg-white min-h-screen">
        <AutoIframe src={aboutData.content_file} />
      </main>
    );
  }

  return (
    <main className="bg-[#F7FAF9]">
      <AboutInstituteHero data={aboutData} />
      <AboutFocusAreas faqs={aboutData.faqs} />
    </main>
  );
};

export default AboutInstitute;
