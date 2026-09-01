import { Skeleton } from "@/components/ui/skeleton"
import { useGetWebsiteSettingQuery } from "@/features/about/api/aboutApi"
import { Mail, MapPin, Phone } from "lucide-react"
import React from "react"
import { Link } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

export default function Footer() {
  const { data, isLoading } = useGetWebsiteSettingQuery()
  const settingData = data?.data?.website_setting

  return (
    <footer className="relative z-10 w-full border-t border-white/5 bg-primary px-6 py-12 font-sans text-white md:px-12 md:py-16 lg:px-20">
      <div className="container mx-auto px-4 md:px-8">
        <div className="grid grid-cols-1 gap-10 border-b border-white/10 pb-12 md:grid-cols-12 md:gap-8">
          <div className="space-y-6 md:col-span-5">
            <div className="space-y-2">
              <h2 className="font-serif text-4xl font-light tracking-[0.08em] text-white md:text-5xl">
                {settingData?.company_name || "GIHQS"}
              </h2>

              <div className="flex max-w-60 items-center space-x-2">
                <div className="h-px grow bg-white/40" />
                <span className="text-[7px] font-bold tracking-[0.2em] whitespace-nowrap text-white/80 uppercase md:text-[8px]">
                  Global Institute For
                </span>
                <div className="h-px grow bg-white/40" />
              </div>

              <p className="pt-0.5 text-[9px] font-bold tracking-[0.18em] text-white/90 uppercase md:text-[10px]">
                Healthcare Quality & Safety
              </p>
            </div>

            <p className="max-w-sm text-sm leading-snug font-medium text-[#B8C5C0]">
              {settingData?.tag_line ||
                "Advancing Healthcare Professionals for High-Reliability Healthcare Systems"}
            </p>

            <div>
              <span className="inline-block rounded-full border border-white/10 bg-[#214038] px-4 py-2 text-sm text-white">
                Towards Zero Preventable Harm
              </span>
            </div>
          </div>

          <div className="space-y-4 md:col-span-3 md:pl-4">
            <h3 className="text-sm font-bold tracking-wider text-white uppercase">
              GIHQS
            </h3>
            <ul className="space-y-3">
              {[
                { title: "Our Story", href: ROUTES.ABOUT_INSTITUTE },
                {
                  title: "Vision, Mission & Values",
                  href: ROUTES.ABOUT_MISSION,
                },
                {
                  title: "Learning",
                  href: ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE,
                },
                { title: "Accreditation", href: ROUTES.ACCREDITATION },
                { title: "Membership", href: ROUTES.MEMBERSHIP },
                { title: "Contact Us", href: ROUTES.CONTACT },
              ].map((link) => (
                <li key={link.title}>
                  <Link
                    to={link.href}
                    className="block text-sm font-normal text-[#B8C5C0] transition-colors duration-150 hover:text-white"
                  >
                    {link.title}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div className="space-y-4 md:col-span-4">
            <h3 className="text-sm font-bold tracking-wider text-white uppercase">
              Contact
            </h3>
            <ul className="space-y-4 text-sm font-normal text-[#B8C5C0]">
              <li className="flex items-start space-x-3">
                <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-[#CAA24A]" />
                {isLoading ? (
                  <Skeleton className="h-10 w-48 bg-white/10" />
                ) : (
                  <span className="leading-relaxed">
                    {settingData?.company_address ? (
                      settingData.company_address
                        .split(/\r?\n/)
                        .map((line, idx) => (
                          <React.Fragment key={idx}>
                            {line}
                            <br />
                          </React.Fragment>
                        ))
                    ) : (
                      <>
                        1209 Mountain Road PL NE
                        <br />
                        STE R Albuquerque, NM 87110
                      </>
                    )}
                  </span>
                )}
              </li>

              <li className="flex items-center space-x-3">
                <Phone className="h-4 w-4 shrink-0 text-[#CAA24A]" />
                {isLoading ? (
                  <Skeleton className="h-5 w-32 bg-white/10" />
                ) : (
                  <a
                    href={`tel:${settingData?.phone_number?.replace(/[^0-9+]/g, "")}`}
                    className="transition-colors hover:text-white"
                  >
                    {settingData?.phone_number || "+1 347 763 9554"}
                  </a>
                )}
              </li>

              {/*K Emails / Support Rows */}
              <li className="flex items-start space-x-3">
                <Mail className="mt-0.5 h-4 w-4 shrink-0 text-[#CAA24A]" />
                <div className="space-y-1">
                  {isLoading ? (
                    <Skeleton className="h-5 w-32 bg-white/10" />
                  ) : (
                    <a
                      href={`mailto:${settingData?.primary_email || "info@gihqs.com"}`}
                      className="block transition-colors hover:text-white"
                    >
                      {settingData?.primary_email || "info@gihqs.com"}
                    </a>
                  )}
                  <span className="block text-xs leading-normal font-light text-muted-foreground">
                    Refunds & purchase support:
                    <br />
                    <a
                      href={`mailto:${settingData?.support_email || "support@gihqs.com"}`}
                      className="text-[#B8C5C0] transition-colors hover:text-white"
                    >
                      {settingData?.support_email || "support@gihqs.com"}
                    </a>
                  </span>
                </div>
              </li>
            </ul>
          </div>
        </div>

        {/* BOTTOM METRICS BAR: COPYRIGHT & COMPLIANCE STACK */}
        <div className="flex flex-col items-center justify-between gap-4 pt-8 text-xs font-normal text-muted-foreground md:flex-row">
          {/* Copyright info statement */}
          <div className="order-2 text-center text-[#8FA89F]/80 md:order-1 md:text-left">
            {settingData?.copyright_text ||
              "© 2026 Global Institute for Healthcare Quality & Safety (GIHQS). All rights reserved."}
          </div>

          {/* Global Legal Link Architecture */}
          <div className="order-1 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-[#8FA89F]/80 md:order-2">
            {[
              { title: "Privacy Policy", slug: "privacy-policy" },
              { title: "Terms of Use", slug: "terms-of-use" },
              {
                title: "Terms & Conditions of Purchase",
                slug: "terms-purchase",
              },
              { title: "Refund Policy", slug: "refund-policy" },
              { title: "Disclaimer", slug: "disclaimer" },
            ].map((policy, idx, arr) => (
              <React.Fragment key={policy.slug}>
                <Link
                  to={`/page/${policy.slug}`}
                  className="whitespace-nowrap transition-colors hover:text-white"
                >
                  {policy.title}
                </Link>
                {idx !== arr.length - 1 && (
                  <span className="hidden text-white/10 sm:inline">|</span>
                )}
              </React.Fragment>
            ))}
          </div>
        </div>
      </div>
    </footer>
  )
}
