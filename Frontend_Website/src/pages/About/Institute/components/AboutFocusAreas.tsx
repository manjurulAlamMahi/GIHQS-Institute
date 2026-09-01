import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion"
import type { Faq } from "@/types/about.types"
import { Minus, Plus } from "lucide-react"

interface AboutFocusAreasProps {
  faqs: Faq[]
}

export default function AboutFocusAreas({ faqs }: AboutFocusAreasProps) {
  if (!faqs || faqs.length === 0) return null

  return (
    <section className="container mx-auto px-4 pt-8 pb-16 sm:px-6 lg:px-8">
      <Accordion type="multiple" className="flex flex-col gap-5">
        {faqs.map((faq) => (
          <AccordionItem
            key={faq.id}
            value={`item-${faq.id}`}
            className="rounded-lg border border-[#D9E5E1] bg-white px-6 shadow-[0_8px_24px_rgba(15,47,38,0.04)]"
          >
            <AccordionTrigger className="min-h-16 items-center py-5 text-base font-bold text-[#10372D] hover:no-underline *:data-[slot=accordion-trigger-icon]:hidden">
              <span className="text-left">{faq.faq_title}</span>
              <span className="ml-5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#0F4A3B] text-white shadow-[0_8px_18px_rgba(15,74,59,0.24)]">
                <Plus className="h-4 w-4 stroke-3 group-aria-expanded/accordion-trigger:hidden" />
                <Minus className="hidden h-4 w-4 stroke-3 group-aria-expanded/accordion-trigger:block" />
              </span>
            </AccordionTrigger>
            <AccordionContent className="pb-6 text-sm leading-relaxed text-[#263F38]">
              {faq.faq_short_description}
            </AccordionContent>
          </AccordionItem>
        ))}
      </Accordion>
    </section>
  )
}
