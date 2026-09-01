import type { ReactNode } from "react"

type ApplicationSectionProps = {
  title: string
  children: ReactNode
}

export function ApplicationSection({ title, children }: ApplicationSectionProps) {
  return (
    <section className="rounded-[14px] bg-white p-6 shadow-sm">
      <h2 className="text-[20px] font-semibold text-[#14392f]">{title}</h2>
      <div className="mt-5">{children}</div>
    </section>
  )
}
