import type { ReactNode } from "react"

export default function ApplicationFormSection({
  number,
  title,
  description,
  children,
}: {
  number: string
  title: string
  description: string
  children: ReactNode
}) {
  return (
    <section className="rounded-3xl border border-[#d7e1de] bg-white p-6 shadow-[0_12px_30px_rgba(15,47,38,0.04)] md:p-7">
      <div className="mb-6">
        <h2 className="font-serif text-xl font-medium text-[#0F2F26]">
          {number}. {title}
        </h2>
        <p className="mt-1 text-sm leading-relaxed text-[#5d756f]">
          {description}
        </p>
      </div>
      {children}
    </section>
  )
}
