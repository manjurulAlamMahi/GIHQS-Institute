interface SectionTitleProps {
  label: string;
  className?: string;
}

export default function SectionTitle({ label, className = "" }: SectionTitleProps) {
  return (
    <div className={`flex w-full items-center justify-center ${className}`}>
      <div className="h-px max-w-xs grow bg-[#d1dddb] md:max-w-md" />
      <span className="mx-4 whitespace-nowrap text-xs font-semibold uppercase tracking-[0.25em] text-[#5b8276]">
        {label}
      </span>
      <div className="h-px max-w-xs grow bg-[#d1dddb] md:max-w-md" />
    </div>
  );
}
