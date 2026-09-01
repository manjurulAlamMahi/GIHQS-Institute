import { Badge } from "@/components/ui/badge";

export default function LearningPathHeader() {
    return (
        <div className="space-y-4 font-sans">
            {/* Category Mini-Label */}
            <span className="text-[10px] md:text-xs font-bold tracking-widest uppercase text-[#A57C1B]">
                Course Lessons
            </span>

            {/* Main Title Headings */}
            <h2 className="text-2xl md:text-4xl font-bold tracking-tight text-[#0a2f1d] font-sans">
                Lean Healthcare Learning Path (10 Lessons)
            </h2>

            {/* Course Context Description */}
            <p className="text-xs md:text-sm text-neutral-500 max-w-4xl leading-relaxed font-light">
                This professional learning module is organized into{" "}
                <strong className="font-semibold text-neutral-800">10 short lessons</strong>.
                Learners can progress lesson by lesson and then complete the final Lean Healthcare assessment.
            </p>

            {/* Navigation / Milestone Phase Filters Group */}
            <div className="flex flex-wrap items-center gap-2.5 pt-2">
                <Badge
                    variant="secondary"
                    className="bg-[#D2E1E0] hover:bg-[#D2E1E0]/80 text-[#1a5c4a] rounded-full px-3.5 h-8 text-[11px] font-medium border-none shadow-none tracking-normal normal-case"
                >
                    Lesson 1–3 • Foundations
                </Badge>
                <Badge
                    variant="secondary"
                    className="bg-[#D2E1E0] hover:bg-[#D2E1E0]/80 text-[#1a5c4a] rounded-full px-3.5 h-8 text-[11px] font-medium border-none shadow-none tracking-normal normal-case"
                >
                    Lesson 4–7 • Lean Tools
                </Badge>
                <Badge
                    variant="secondary"
                    className="bg-[#D2E1E0] hover:bg-[#D2E1E0]/80 text-[#1a5c4a] rounded-full px-3.5 h-8 text-[11px] font-medium border-none shadow-none tracking-normal normal-case"
                >
                    Lesson 8–10 • Leadership & Sustainment
                </Badge>
            </div>
        </div>
    );
}