import { Button } from "@/components/ui/button"
import { Skeleton } from "@/components/ui/skeleton"
import {
  useGetPathwaysStartQuery,
  useLazyGetPathwayStepQuery,
} from "@/features/home/api/homeApi"
import { ROUTES } from "@/routes/routes.constants"
import type {
  PathwayOption,
  PathwayQuestionData,
  PathwayResultData,
} from "@/types/home.types"
import { ChevronRight, Loader2 } from "lucide-react"
import * as React from "react"
import { Link } from "react-router"

const tags = [
  { label: "Professional Certifications", href: ROUTES.CERTIFICATION },
  {
    label: "Learning Catalogue",
    href: ROUTES.PROFESSIONAL_DEVELOPMENT_CATALOGUE,
  },
  { label: "Institutional Accreditation", href: ROUTES.ACCREDITATION },
  { label: "Advisory Services", href: ROUTES.ADVISORY },
]

const optionButtonClasses =
  "group h-auto min-h-14 w-full justify-between whitespace-normal rounded-xl border-[#DDE8E4] bg-white px-4 py-4 text-left text-sm font-semibold leading-snug text-[#0F2F26] transition-all hover:border-[#D4AA3A]/50 hover:bg-white md:px-5 md:text-base"

export default function Hero() {
  const { data: startData, isLoading: isStartLoading } =
    useGetPathwaysStartQuery()
  const [triggerGetStep, { isLoading: isStepLoading }] =
    useLazyGetPathwayStepQuery()

  const [currentQuestion, setCurrentQuestion] =
    React.useState<PathwayQuestionData | null>(null)
  const [resultData, setResultData] = React.useState<PathwayResultData | null>(
    null
  )
  const [history, setHistory] = React.useState<
    Array<{
      stepNumber: number
      question: string
      selectedOption: PathwayOption
    }>
  >([])

  React.useEffect(() => {
    if (startData?.data && history.length === 0 && !resultData) {
      if (startData.type === "question") {
        setCurrentQuestion(startData.data as PathwayQuestionData)
      }
    }
  }, [startData, history.length, resultData])

  const currentStepNumber = resultData ? 4 : currentQuestion?.step_number || 1
  const totalSteps = 4

  const handleSelectOption = async (option: PathwayOption) => {
    if (!currentQuestion) return

    const newHistory = [
      ...history,
      {
        stepNumber: currentQuestion.step_number,
        question: currentQuestion.question_text,
        selectedOption: option,
      },
    ]
    setHistory(newHistory)

    try {
      const res = await triggerGetStep(option.id).unwrap()
      if (res?.type === "result") {
        setResultData(res.data as PathwayResultData)
        setCurrentQuestion(null)
      } else if (res?.type === "question" && res.data) {
        setCurrentQuestion(res.data as PathwayQuestionData)
      } else if (res?.data) {
        if ("options" in res.data) {
          setCurrentQuestion(res.data as PathwayQuestionData)
        } else {
          setResultData(res.data as PathwayResultData)
          setCurrentQuestion(null)
        }
      }
    } catch (error) {
      console.error("Failed to fetch step:", error)
    }
  }

  const restartPathway = () => {
    setHistory([])
    setResultData(null)
    if (startData?.data && startData.type === "question") {
      setCurrentQuestion(startData.data as PathwayQuestionData)
    }
  }

  const isLoading = isStartLoading || isStepLoading

  return (
    <div className="relative container mx-auto my-4 flex h-auto flex-col items-center justify-center gap-10 overflow-hidden rounded-3xl bg-[#0F2F26] px-6 py-12 md:my-10 md:h-150 md:flex-row md:p-20 md:px-12">
      <div
        className="pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-85 blur-3xl"
        style={{
          background:
            "radial-gradient(140% 140% at 72% 50%, rgba(212, 170, 58, 0.22) 0%, rgba(212, 170, 58, 0.12) 24%, rgba(212, 170, 58, 0.06) 48%, rgba(15, 47, 38, 0) 80%)",
        }}
      />

      <div className="relative z-10 w-full space-y-8 md:w-1/2">
        <div className="inline-flex max-w-full items-center rounded-full border border-[rgba(240,208,112,0.72)] bg-[rgba(240,208,112,0.10)] px-3.5 py-2 text-xs leading-tight font-semibold tracking-wider text-[#D4AA3A] uppercase">
          GIHQS Professional Pathways
        </div>

        <div className="space-y-2">
          <h1 className="text-4xl leading-tight font-medium text-white md:text-5xl">
            Begin Your Pathway to
          </h1>
          <h1 className="font-serif text-4xl leading-tight text-[#D4AA3A] italic md:text-5xl">
            High-Reliability Healthcare Leadership
          </h1>
        </div>

        <p className="max-w-lg text-base leading-relaxed text-[#B8C5C0] md:text-lg">
          Answer three short questions to navigate to the most relevant GIHQS
          certification, professional development catalogue, accreditation
          pathway, or advisory service.
        </p>

        <div className="space-y-2">
          <div className="flex gap-2">
            {Array.from({ length: totalSteps }, (_, index) => index + 1).map(
              (step) => (
                <div
                  key={step}
                  className={`h-1.5 w-8 rounded-full transition-all ${
                    step === currentStepNumber ? "bg-[#D4AA3A]" : "bg-[#1A3C32]"
                  }`}
                />
              )
            )}
          </div>
          <p className="text-xs font-medium tracking-wide text-[#8FA89F]">
            Step {currentStepNumber} of {totalSteps}
          </p>
        </div>

        <div className="flex flex-wrap gap-3">
          {tags.map((tag) => (
            <Link
              key={tag.label}
              to={tag.href}
              className="max-w-full min-w-0 rounded-full border border-[#1A3C32] px-4 py-2 text-xs leading-tight font-medium text-[#8FA89F] transition-colors hover:border-[#D4AA3A]/40 hover:text-[#D4AA3A] md:text-sm"
            >
              {tag.label}
            </Link>
          ))}
        </div>
      </div>

      <div className="relative z-10 flex w-full items-center justify-center md:w-1/2">
        <div className="w-full max-w-xl overflow-hidden rounded-[24px] bg-[#F4F8F7] p-5 shadow-2xl sm:p-6 md:p-8">
          <div className="space-y-3">
            <p className="text-xs leading-tight font-bold tracking-[0.15em] text-[#6B7F78] uppercase">
              Begin Your Pathway
            </p>
            <h2 className="text-2xl leading-snug font-semibold text-[#0F2F26] md:text-3xl">
              {isStartLoading && !currentQuestion && !resultData ? (
                <Skeleton className="h-8 w-3/4" />
              ) : resultData ? (
                resultData.title
              ) : (
                currentQuestion?.question_text
              )}
            </h2>

            {history.length > 0 && !resultData && (
              <div className="space-y-1 text-sm leading-relaxed text-[#4A5F57]">
                {history.map((h, i) => (
                  <p key={i}>
                    {i === 0 ? "Role: " : "Interest: "}
                    <span className="font-semibold wrap-break-word text-[#0F2F26]">
                      {h.selectedOption.option_text}
                    </span>
                  </p>
                ))}
              </div>
            )}

            {resultData && (
              <div className="space-y-2 text-sm leading-relaxed text-[#4A5F57]">
                <p>{resultData.description}</p>
                {history.length > 0 && (
                  <p>
                    Selected route:{" "}
                    <span className="font-semibold text-[#0F2F26]">
                      {history[history.length - 1]?.selectedOption.option_text}
                    </span>
                  </p>
                )}
              </div>
            )}
          </div>

          <div className="mt-7 space-y-3">
            {isLoading && !currentQuestion && !resultData ? (
              <div className="space-y-3">
                <Skeleton className="h-14 w-full rounded-xl" />
                <Skeleton className="h-14 w-full rounded-xl" />
                <Skeleton className="h-14 w-full rounded-xl" />
              </div>
            ) : currentQuestion?.options ? (
              currentQuestion.options.map((option) => (
                <Button
                  key={option.id}
                  variant="outline"
                  className={optionButtonClasses}
                  disabled={isLoading}
                  onClick={() => handleSelectOption(option)}
                  type="button"
                >
                  <span className="min-w-0 flex-1 wrap-break-word whitespace-normal">
                    {option.option_text}
                  </span>
                  {isStepLoading ? (
                    <Loader2 className="ml-4 h-4 w-4 shrink-0 animate-spin text-[#4A6B5F]" />
                  ) : (
                    <ChevronRight className="ml-4 h-4 w-4 shrink-0 text-[#4A6B5F] transition-colors group-hover:text-[#D4AA3A]" />
                  )}
                </Button>
              ))
            ) : resultData ? (
              <>
                {resultData.badges && resultData.badges.length > 0 && (
                  <div className="flex flex-wrap gap-2">
                    {resultData.badges.map((badge) => (
                      <span
                        key={badge}
                        className="rounded-full border border-[rgba(212,170,58,0.48)] px-3 py-1 text-[11px] font-medium text-[#B08A24]"
                      >
                        {badge}
                      </span>
                    ))}
                  </div>
                )}

                {resultData.info_box_text && (
                  <div className="rounded-xl border border-[#E5EDE9] bg-white/50 p-3 text-xs leading-relaxed text-[#4A5F57] md:text-sm">
                    {resultData.info_box_text}
                  </div>
                )}

                <div className="flex flex-col gap-3 pt-1 sm:flex-row">
                  {resultData.primary_button_text && (
                    <Button
                      asChild
                      variant="default"
                      className="h-auto min-h-12 flex-1 rounded-full bg-[#0F4A3B] px-5 py-3 text-sm leading-tight font-semibold whitespace-normal text-white hover:bg-[#0A3328]"
                    >
                      {resultData.primary_button_url.startsWith("http") ? (
                        <a
                          href={resultData.primary_button_url}
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          <span className="wrap-break-word">
                            {resultData.primary_button_text}
                          </span>
                        </a>
                      ) : (
                        <Link
                          to={
                            resultData.primary_button_url ||
                            ROUTES.ACCREDITATION
                          }
                        >
                          <span className="wrap-break-word">
                            {resultData.primary_button_text}
                          </span>
                        </Link>
                      )}
                    </Button>
                  )}

                  {resultData.secondary_button_text && (
                    <Button
                      asChild
                      variant="outline"
                      className="h-auto min-h-12 flex-1 rounded-full border-[#E5EDE9] px-5 py-3 text-sm leading-tight font-semibold whitespace-normal text-[#0F4A3B] hover:bg-[#F4F8F7]"
                    >
                      {resultData.secondary_button_url?.startsWith("http") ||
                      resultData.secondary_button_url?.endsWith(".pdf") ? (
                        <a
                          href={resultData.secondary_button_url}
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          <span className="wrap-break-word">
                            {resultData.secondary_button_text}
                          </span>
                        </a>
                      ) : (
                        <Link
                          to={
                            resultData.secondary_button_url ||
                            ROUTES.ACCREDITATION
                          }
                        >
                          <span className="wrap-break-word">
                            {resultData.secondary_button_text}
                          </span>
                        </Link>
                      )}
                    </Button>
                  )}
                </div>
              </>
            ) : null}
          </div>

          {(history.length > 0 || resultData) && (
            <button
              className="mt-6 text-xs font-bold text-[#0D3B31] hover:underline"
              onClick={restartPathway}
              type="button"
            >
              Start Over
            </button>
          )}
        </div>
      </div>
    </div>
  )
}
