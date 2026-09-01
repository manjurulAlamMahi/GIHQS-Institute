import AdvisoryRequestForm from "./_components/AdvisoryRequestForm";
import RequestAdvisoryHero from "./_components/RequestAdvisoryHero";

export default function RequestAdvisoryConsultationPage() {
    return (
        <main className="w-full md:container mx-auto px-4 md:px-8 my-10">
            <header className="w-full">
                <RequestAdvisoryHero />
            </header>
            <main className="w-full mt-2">
                <AdvisoryRequestForm />
            </main>
        </main>
    );
}