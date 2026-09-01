import { useState } from "react"
import { Link, useNavigate, useParams } from "react-router"
import { ArrowLeft, CheckCircle, Loader2 } from "lucide-react"
import { useGetExamQuestionsQuery, useSubmitExamMutation } from "@/features/profile/api/profileApi"
import { ROUTES } from "@/routes/routes.constants"
import { Skeleton } from "@/components/ui/skeleton"

export default function DashboardExamPage() {
  const { examId } = useParams()
  const navigate = useNavigate()
  const { data, isLoading, isError } = useGetExamQuestionsQuery(examId as string, { skip: !examId })
  const [submitExam, { isLoading: submitting }] = useSubmitExamMutation()
  const [answers, setAnswers] = useState<Record<number, number>>({})
  const [result, setResult] = useState<NonNullable<Awaited<ReturnType<typeof submitExam>>["data"]>["data"]["result"] | null>(null)
  const exam = data?.data?.exam

  if (isLoading) return <section className="p-6"><Skeleton className="h-10 w-72 mb-6" /><Skeleton className="h-32 w-full" /></section>
  if (isError || !exam) return <section className="p-8 text-center text-neutral-500">Exam questions could not be loaded.</section>

  const handleSubmit = async () => {
    const response = await submitExam({ id: exam.id, duration: 0, answers: Object.entries(answers).map(([question_id, option_id]) => ({ question_id: Number(question_id), option_id })) }).unwrap()
    setResult(response.data.result)
  }

  if (result) return <section className="min-h-full bg-[#f4f6f7] px-5 py-8"><div className="mx-auto max-w-2xl rounded-2xl bg-white p-8 text-center shadow-sm"><CheckCircle className="mx-auto mb-4 h-14 w-14 text-[#008a42]" /><h1 className="mb-2 text-3xl font-serif font-semibold text-[#14392f]">Exam Submitted</h1><p className="mb-6 text-neutral-600">Your score is {result.score}/{result.points_available} ({result.percentage}%). Status: <strong>{result.status}</strong>.</p>{result.status === "passed" && result.download_certificate && <a className="mr-3 inline-flex rounded-lg bg-[#ddb737] px-5 py-3 font-semibold text-[#14392f]" href={result.download_certificate} target="_blank" rel="noreferrer">Download Certificate</a>}<button onClick={() => navigate(ROUTES.DASHBOARD_COURSE_DETAIL.replace(":id", exam.catalogue_id.toString()))} className="inline-flex rounded-lg bg-[#14392f] px-5 py-3 font-semibold text-white">Back to Course</button></div></section>

  return <section className="min-h-full bg-[#f4f6f7] px-5 py-6"><div className="mx-auto max-w-3xl"><Link to={ROUTES.DASHBOARD_COURSE_DETAIL.replace(":id", exam.catalogue_id.toString())} className="mb-6 inline-flex items-center gap-2 font-semibold text-[#14392f]"><ArrowLeft className="h-4 w-4" />Back to Course</Link><div className="mb-6 rounded-2xl bg-[#14392f] p-7 text-white"><h1 className="text-3xl font-serif font-semibold">{exam.exam_title}</h1><p className="mt-2 text-[#d8e4df]">Answer all questions and submit your exam.</p></div><div className="space-y-5">{[...exam.questions].sort((a,b) => a.sort_order-b.sort_order).map((question, index) => <div key={question.id} className="rounded-2xl border border-border bg-white p-6 shadow-sm"><h2 className="mb-4 font-semibold text-[#111827]">{index + 1}. {question.question_text}</h2><div className="space-y-3">{[...question.options].sort((a,b) => a.sort_order-b.sort_order).map(option => <label key={option.id} className={`flex cursor-pointer items-center gap-3 rounded-lg border p-3 ${answers[question.id] === option.id ? "border-[#14392f] bg-[#edf5f1]" : "border-border"}`}><input type="radio" name={`question-${question.id}`} checked={answers[question.id] === option.id} onChange={() => setAnswers({...answers, [question.id]: option.id})} />{option.option_text}</label>)}</div></div>)}</div><button disabled={submitting} onClick={handleSubmit} className="mt-6 inline-flex h-12 items-center gap-2 rounded-xl bg-[#14392f] px-7 font-semibold text-white disabled:opacity-60">{submitting && <Loader2 className="h-4 w-4 animate-spin" />}Submit Exam</button></div></section>
}
