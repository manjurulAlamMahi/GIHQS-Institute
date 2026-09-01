
import { Card, CardContent } from "@/components/ui/card";

const benefitsList = [
    {
        title: "Professional Recognition",
        desc: "Demonstrate advanced competency in healthcare AI governance and patient safety to employers and peers."
    },
    {
        title: "Leadership Readiness",
        desc: "Prepare to lead AI oversight committees, safety programs, and digital health governance initiatives."
    },
    {
        title: "Evidence-Based Competency",
        desc: "Built on healthcare quality science, patient safety principles, and responsible AI governance frameworks."
    },
    {
        title: "Future-Proof Your Career",
        desc: "Position yourself at the forefront of one of the fastest-growing areas of healthcare professional practice."
    }
];

export default function CertAbout() {
    return (
        <section className="w-full font-sans my-10">
            <div className="bg-white border border-neutral-100 rounded-3xl p-6 md:p-10 space-y-10 shadow-sm">

                {/* Heading Segment description text blocks */}
                <div className="space-y-3">
                    <span className="text-[10px] md:text-xs font-bold tracking-widest uppercase text-[#A57C1B]">
                        About
                    </span>
                    <h2 className="text-2xl md:text-4xl font-serif text-[#0F2F26] font-medium tracking-wide">
                        Why earn the{" "}
                        <span className="italic text-[#A57C1B] font-medium font-serif">
                            AIHQSP
                        </span>{" "}
                        Credential?
                    </h2>
                    <p className="text-xs md:text-sm text-neutral-600 leading-relaxed font-light max-w-6xl">
                        Artificial intelligence is transforming healthcare at speed. Clinical decision support, diagnostic algorithms, patient monitoring systems, and operational AI tools are now embedded across care delivery – bringing new opportunities and new risks that healthcare professionals must be equipped to manage.
                    </p>
                    <p className="text-xs md:text-sm text-neutral-600 leading-relaxed font-light max-w-6xl">
                        The AIHQSP certification validates the knowledge and professional competency required to oversee, govern, and continuously improve AI systems used in healthcare – ensuring they remain safe, ethical, and aligned with patient safety standards.
                    </p>
                </div>

                {/* 2x2 Grid of informational cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    {benefitsList.map((benefit, index) => (
                        <Card key={index} className="rounded-2xl border-neutral-100/70 bg-[#fafafa]/40 shadow-none">
                            <CardContent className="p-6 space-y-3">
                                <h3 className="text-base md:text-lg font-bold tracking-tight text-neutral-900 leading-snug">
                                    {benefit.title}
                                </h3>
                                <p className="text-xs text-neutral-600 leading-relaxed font-normal">
                                    {benefit.desc}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

            </div>
        </section>
    );
}