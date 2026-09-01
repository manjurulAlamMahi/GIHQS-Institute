type DashboardComingSoonProps = {
  title: string
  description: string
}

export function DashboardComingSoon({
  title,
  description,
}: DashboardComingSoonProps) {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-6 py-8">
      <div className="mx-auto flex min-h-90 max-w-5xl items-center justify-center rounded-lg border border-border bg-card p-8 text-center shadow-sm">
        <div className="max-w-md">
          <p className="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">
            Dashboard
          </p>
          <h1 className="font-heading text-3xl font-semibold text-foreground">
            {title}
          </h1>
          <p className="mt-3 text-sm leading-6 text-muted-foreground">
            {description}
          </p>
          <div className="mx-auto mt-6 inline-flex h-9 items-center rounded-md border border-[#ddb737]/50 bg-[#ddb737]/15 px-4 text-sm font-medium text-[#14392f]">
            Coming soon
          </div>
        </div>
      </div>
    </section>
  )
}
