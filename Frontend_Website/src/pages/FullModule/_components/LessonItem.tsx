
interface LessonItemProps {
    step: number;
    title: string;
    description: string;
    isLast: boolean;
}

export default function LessonItem({ step, title, description, isLast }: LessonItemProps) {
    return (
        <div className={`flex gap-4 md:gap-6 group relative ${!isLast ? 'pb-6' : ''}`}>

            {!isLast && (
                <span className="absolute left-[18px] top-9 md:top-10 bottom-0 w-[2px] bg-neutral-200" />
            )}

            {/* Step circle */}
            <div className="shrink-0 z-10">
                <div className="w-9 h-9 md:w-10 md:h-10 rounded-full bg-[#DCC27F] text-[#A57C1B] font-bold text-sm md:text-base flex items-center justify-center shadow-sm border border-[#EDE5D1]">
                    {step}
                </div>
            </div>

            {/* Lesson card */}
            <div className="grow bg-[#f8faf9] border border-neutral-200/60 hover:border-neutral-300 rounded-xl p-4 md:p-5 transition-all duration-200 shadow-none">
                <h3 className="text-sm md:text-base font-bold text-neutral-900 tracking-tight mb-1">
                    {title}
                </h3>
                <p className="text-xs md:text-sm text-neutral-500 font-normal leading-relaxed">
                    {description}
                </p>
            </div>
        </div>
    );
}