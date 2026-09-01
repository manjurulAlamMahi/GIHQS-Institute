import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import CertificationImage from "@/assets/images/AIHQSP Certification Badge.png"
const examDetails = [
    { label: "Questions", value: "150" },
    { label: "Duration", value: "3 Hours" },
    { label: "Passing Score", value: "600 / 800" },
    { label: "Domains", value: "12" },
    { label: "Delivery", value: "Online Proctored" },
];

export default function CertHero() {
    return (
        <div className="w-full font-sans antialiased selection:bg-[#0F2F26]/10">
            <div
                style={{
                    borderRadius: "24px",
                    background: "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)"
                }}
                className="p-8 md:p-12 text-white shadow-xl relative grid grid-cols-1 md:grid-cols-12 gap-8 items-start"
            >

                <div className="md:col-span-7 space-y-6">
                    <div className="inline-block rounded-full bg-white/10 px-3 py-1 border border-white/5 backdrop-blur-sm">
                        <span className="text-[10px] md:text-xs font-semibold tracking-widest uppercase text-yellow-500/90">
                            GIHQS Professional Certification
                        </span>
                    </div>

                    {/* Main Certification Header */}
                    <h1 className="text-3xl md:text-5xl font-serif font-normal tracking-tight leading-tight">
                        AI Healthcare Quality &{" "}
                        <span className="italic text-[#F0D070] font-normal font-serif">
                            Safety Professional
                        </span>
                    </h1>

                    {/* Description Copy */}
                    <p className="text-xs md:text-sm text-neutral-300 leading-relaxed font-sans font-light max-w-xl">
                        Lead safe, high-reliability AI adoption in healthcare. The AIHQSP certification validates advanced competency across clinical AI safety, governance, human factors, algorithmic bias, real-world monitoring, and continuous quality improvement.
                    </p>

                    {/* Core Call to Action Buttons */}
                    <div className="flex flex-wrap items-center gap-4 pt-2">
                        <Button
                            type="button"
                            className="h-11 px-6 rounded-full bg-[#facc15] hover:bg-[#eab308] text-neutral-900 font-bold text-xs tracking-wide shadow-none transition-colors border-none"
                        >
                            Apply for AIHQSP Certification
                        </Button>
                        <Button
                            type="button"
                            className="h-11 px-6 rounded-full bg-white/5 hover:bg-white/10 border border-white/20 text-white font-semibold text-xs tracking-wide transition-colors shadow-none"
                        >
                            Download Study Guide
                        </Button>
                    </div>

                    {/* Taxonomy Tags Pills Group */}
                    <div className="flex flex-wrap items-center gap-2 pt-4">
                        {[
                            "AI Safety Science",
                            "Clinical Governance",
                            "Human Governance",
                            "Human Factors",
                            "Quality Improvement"
                        ].map((tag) => (
                            <Badge
                                key={tag}
                                variant="secondary"
                                className="bg-white/5 text-neutral-300 rounded-full px-3.5 py-1 text-[11px] font-medium border border-white/10 shadow-none tracking-normal normal-case"
                            >
                                {tag}
                            </Badge>
                        ))}
                    </div>
                </div>

                <div className="md:col-span-5 flex flex-col items-center md:items-end gap-6 w-full">
                    <div className="text-center md:text-right space-y-2">
                        <div className="w-48 h-48 rounded-full border border-white/5 flex items-center justify-center text-white/50 text-[10px] select-none mx-auto md:mr-0">
                            <img className="w-full h-full object-contain" src={CertificationImage} alt="AIHQSP Logo" />
                        </div>
                        <span className="text-[11px] font-bold tracking-widest text-neutral-400 uppercase">
                            AIHQSP
                        </span>
                    </div>

                    {/* Information Stat Board Card using backdrop blur effects */}
                    <div className="bg-white/5 border border-white/5 rounded-2xl p-6 md:p-8 backdrop-blur-sm shadow-xl w-full max-w-105">
                        <span className="block text-[10px] uppercase tracking-widest text-[#F0D070]/90 font-bold mb-6">
                            Exam At A Glance
                        </span>

                        {/* Stats list rows */}
                        <div className="space-y-4">
                            {examDetails.map((detail) => (
                                <div key={detail.label} className="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 sm:gap-2 pb-3 border-b border-white/10 last:border-none">
                                    <span className="text-xs text-neutral-400 font-medium">
                                        {detail.label}
                                    </span>
                                    <span className="text-sm md:text-base font-bold text-white leading-none">
                                        {detail.value}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    );
}