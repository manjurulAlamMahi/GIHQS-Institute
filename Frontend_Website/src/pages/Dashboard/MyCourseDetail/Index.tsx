import { Skeleton } from "@/components/ui/skeleton"
import {
  useCompleteCatalogueVideoMutation,
  useGetPurchasedCatalogueByIdQuery,
} from "@/features/profile/api/profileApi"
import type {
  PurchasedCatalogueExam,
  PurchasedCatalogueVideoFile,
  PurchasedCatalogueVideoLink,
} from "@/types/profile.types"
import { useQueryModal } from "@/hooks/useQueryModal"
import { ROUTES } from "@/routes/routes.constants"
import {
  AlertCircle,
  ArrowLeft,
  BookOpen,
  CheckCircle,
  Clock,
  Download,
  ExternalLink,
  FileText,
  Lock,
  Play,
  PlayCircle,
} from "lucide-react"
import { useEffect, useRef, useState } from "react"
import { Link, useParams } from "react-router"
import YouTube from "react-youtube"

const getYouTubeVideoId = (url: string) => {
  const match = url.match(
    /(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&]{11})/
  )
  return match ? match[1] : null
}

const CustomVideoPlayer = ({
  video,
  onComplete,
}: {
  video: PurchasedCatalogueVideoFile
  onComplete: (params: { video_id?: number; video_link_id?: number }) => void
}) => {
  const videoRef = useRef<HTMLVideoElement>(null)
  const [isPlaying, setIsPlaying] = useState(false)
  const maxTimeRef = useRef(0)

  const handlePlay = () => {
    if (videoRef.current) {
      videoRef.current.play()
      setIsPlaying(true)
    }
  }

  const handleTimeUpdate = () => {
    if (!videoRef.current) return
    if (!videoRef.current.seeking) {
      if (videoRef.current.currentTime > maxTimeRef.current) {
        maxTimeRef.current = videoRef.current.currentTime
      }
    }
  }

  const handleSeeking = () => {
    if (!videoRef.current) return
    const delta = videoRef.current.currentTime - maxTimeRef.current
    if (delta > 1) {
      videoRef.current.currentTime = maxTimeRef.current
    }
  }

  const handleRateChange = () => {
    if (videoRef.current && videoRef.current.playbackRate > 2) {
      videoRef.current.playbackRate = 2
    }
  }

  return (
    <div className="flex flex-col overflow-hidden rounded-xl border border-border bg-white shadow-sm">
      <div className="group relative aspect-video w-full bg-black">
        <video
          ref={videoRef}
          controls={isPlaying}
          className="h-full w-full object-cover"
          src={video.video_file}
          title={video.video_title}
          preload="metadata"
          poster={video.thumbnail || undefined}
          onTimeUpdate={handleTimeUpdate}
          onSeeking={handleSeeking}
          onRateChange={handleRateChange}
          onEnded={() => {
            if (!video.is_completed) {
              onComplete({ video_id: video.id })
            }
            setIsPlaying(false)
          }}
          onPlay={() => setIsPlaying(true)}
          onPause={() => setIsPlaying(false)}
        >
          Your browser does not support the video tag.
        </video>
        {!isPlaying && (
          <div
            className="absolute inset-0 flex cursor-pointer items-center justify-center bg-black/40 transition-colors hover:bg-black/50"
            onClick={handlePlay}
          >
            <div className="flex h-16 w-16 items-center justify-center rounded-full bg-white/90 text-[#14392f] shadow-lg transition-transform hover:scale-110">
              <Play className="ml-1 h-8 w-8 fill-current" strokeWidth={1} />
            </div>
          </div>
        )}
      </div>
      <div className="p-4">
        <h4
          className="line-clamp-2 font-medium text-[#111827]"
          title={video.video_title}
        >
          {video.video_title}
        </h4>
      </div>
    </div>
  )
}

const CustomYouTubePlayer = ({
  link,
  onComplete,
}: {
  link: PurchasedCatalogueVideoLink
  onComplete: (params: { video_id?: number; video_link_id?: number }) => void
}) => {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const [player, setPlayer] = useState<any>(null)
  const maxTimeRef = useRef(0)
  const videoId = getYouTubeVideoId(link.video_link_url)

  useEffect(() => {
    if (!player) return
    const interval = setInterval(async () => {
      try {
        const currentTime = await player.getCurrentTime()
        if (currentTime - maxTimeRef.current > 2) {
          player.seekTo(maxTimeRef.current, true)
        } else {
          if (currentTime > maxTimeRef.current) {
            maxTimeRef.current = currentTime
          }
        }
      } catch {
        // ignore errors
      }
    }, 1000)
    return () => clearInterval(interval)
  }, [player])

  return (
    <div className="flex flex-col overflow-hidden rounded-xl border border-border bg-white shadow-sm">
      <div className="relative aspect-video w-full bg-neutral-900">
        {videoId ? (
          <YouTube
            videoId={videoId}
            onReady={(e) => setPlayer(e.target)}
            onEnd={() => {
              if (!link.is_completed) {
                onComplete({ video_link_id: link.id })
              }
            }}
            onPlaybackRateChange={(e) => {
              if (e.data > 2) {
                e.target.setPlaybackRate(2)
              }
            }}
            className="absolute top-0 left-0 h-full w-full"
            opts={{
              width: "100%",
              height: "100%",
              playerVars: { autoplay: 0 },
            }}
          />
        ) : (
          <div className="flex h-full w-full items-center justify-center text-sm text-white">
            Invalid YouTube Link
          </div>
        )}
      </div>
      <div className="p-4">
        <h4
          className="line-clamp-2 font-medium text-[#111827]"
          title={link.video_link_title}
        >
          {link.video_link_title}
        </h4>
      </div>
    </div>
  )
}

export default function DashboardCourseDetail() {
  const { id } = useParams()
  const {
    data: response,
    isLoading,
    refetch,
  } = useGetPurchasedCatalogueByIdQuery(id as string, { skip: !id })
  const [completeVideo] = useCompleteCatalogueVideoMutation()
  const tabQuery = useQueryModal("tab", "overview")
  const activeTab = tabQuery.currentValue || "overview"

  const handleVideoEnd = async (params: {
    video_id?: number
    video_link_id?: number
  }) => {
    try {
      await completeVideo({ ...params, is_completed: true }).unwrap()
      const { data } = await refetch()
      const updatedCatalogue = data?.data?.catalogue

      if (updatedCatalogue) {
        const hasTakenAnyExam = updatedCatalogue.exams?.some(
          (exam: PurchasedCatalogueExam) => !!exam.user_status
        )

        const videoFilesCompleted = updatedCatalogue.video_files?.length
          ? updatedCatalogue.video_files.every(
              (v: PurchasedCatalogueVideoFile) => v.is_completed
            )
          : true
        const videoLinksCompleted = updatedCatalogue.video_links?.length
          ? updatedCatalogue.video_links.every(
              (v: PurchasedCatalogueVideoLink) => v.is_completed
            )
          : true
        const areAllVideosCompleted = videoFilesCompleted && videoLinksCompleted

        if (!hasTakenAnyExam && areAllVideosCompleted) {
          tabQuery.open("exams")
        }
      }
    } catch (error) {
      console.error("Failed to mark video as complete", error)
    }
  }

  if (isLoading) {
    return (
      <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
        <Skeleton className="mb-6 h-12 w-32" />
        <Skeleton className="mb-6 h-50 w-full rounded-xl" />
        <Skeleton className="h-100 w-full rounded-xl" />
      </section>
    )
  }

  const catalogue = response?.data?.catalogue

  if (!catalogue) {
    return (
      <section className="flex min-h-full items-center justify-center bg-[#f4f6f7] px-5 py-6">
        <div className="text-center text-neutral-500">
          Course details not found.
        </div>
      </section>
    )
  }

  const hasResources =
    (catalogue.resources && catalogue.resources.length > 0) ||
    (catalogue.live_links && catalogue.live_links.length > 0) ||
    (catalogue.video_files && catalogue.video_files.length > 0) ||
    (catalogue.video_links && catalogue.video_links.length > 0) ||
    (catalogue.html_resources && catalogue.html_resources.length > 0)

  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <Link
        to={ROUTES.DASHBOARD_COURSES}
        className="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-[#14392f] hover:underline"
      >
        <ArrowLeft className="h-4 w-4" />
        Back to My Courses
      </Link>

      {/* Header Card */}
      <div className="mb-6 flex flex-col justify-between gap-6 rounded-2xl bg-[#14392f] p-8 text-white shadow-sm md:flex-row md:items-end">
        <div className="max-w-2xl">
          <span className="mb-4 inline-flex h-8 items-center rounded-full bg-white/20 px-4 text-[13px] font-bold tracking-wider text-white uppercase">
            {catalogue.service_type || "Course"}
          </span>
          <h1 className="mb-2 font-serif text-3xl leading-tight font-semibold">
            {catalogue.title}
          </h1>
          <p className="text-sm text-[#d8e4df]">
            {catalogue.short_description}
          </p>
        </div>
        <button
          type="button"
          className="inline-flex h-12 cursor-default items-center justify-center gap-2 rounded-xl bg-[#ddb737] px-6 font-semibold whitespace-nowrap text-[#14392f] opacity-90 transition-colors"
        >
          <PlayCircle className="h-5 w-5" />
          Continue Learning
        </button>
      </div>

      <div className="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
        <div className="flex border-b border-border">
          <button
            onClick={() => tabQuery.open("overview")}
            className={`h-14 flex-1 border-b-2 text-[16px] font-semibold transition-colors ${activeTab === "overview" ? "border-[#14392f] bg-neutral-50 text-[#14392f]" : "border-transparent text-[#667085] hover:bg-neutral-50 hover:text-[#14392f]"}`}
          >
            Overview
          </button>
          {hasResources && (
            <button
              onClick={() => tabQuery.open("resources")}
              className={`h-14 flex-1 border-b-2 text-[16px] font-semibold transition-colors ${activeTab === "resources" ? "border-[#14392f] bg-neutral-50 text-[#14392f]" : "border-transparent text-[#667085] hover:bg-neutral-50 hover:text-[#14392f]"}`}
            >
              Resources
            </button>
          )}
          <button
            onClick={() => tabQuery.open("exams")}
            className={`h-14 flex-1 border-b-2 text-[16px] font-semibold transition-colors ${activeTab === "exams" ? "border-[#14392f] bg-neutral-50 text-[#14392f]" : "border-transparent text-[#667085] hover:bg-neutral-50 hover:text-[#14392f]"}`}
          >
            Exams & Assessments
          </button>
        </div>

        {/* Content */}
        <div className="min-h-100 p-6 md:p-8">
          {activeTab === "overview" && (
            <div className="prose max-w-none">
              {catalogue.overview_video ? (
                <div className="mb-8 max-w-md overflow-hidden rounded-xl border border-border bg-black shadow-sm">
                  <video
                    controls
                    className="aspect-video w-full object-contain"
                    src={catalogue.overview_video}
                  >
                    Your browser does not support the video tag.
                  </video>
                </div>
              ) : (
                <div className="py-12 text-center text-[#667085]">
                  No overview video available for this course.
                </div>
              )}
            </div>
          )}

          {activeTab === "resources" && (
            <div className="space-y-6">
              {!catalogue.resources?.length &&
              !catalogue.live_links?.length &&
              !catalogue.video_files?.length &&
              !catalogue.video_links?.length &&
              !catalogue.html_resources?.length ? (
                <div className="py-12 text-center text-[#667085]">
                  No resources available.
                </div>
              ) : (
                <>
                  {/* Interactive HTML documents - modules, toolkits, worksheets */}
                  {catalogue.html_resources &&
                    catalogue.html_resources.length > 0 && (
                      <div>
                        <h3 className="mb-3 text-[16px] font-semibold text-[#111827]">
                          Modules & Toolkits
                        </h3>
                        <div className="grid gap-4 md:grid-cols-2">
                          {catalogue.html_resources.map((doc) => (
                            <Link
                              key={doc.id}
                              to={ROUTES.DASHBOARD_HTML_RESOURCE.replace(
                                ":id",
                                catalogue.id.toString()
                              ).replace(":resourceId", doc.id.toString())}
                              className="flex items-center justify-between rounded-xl border border-border bg-[#f8faf9] p-4 transition-colors hover:border-[#14392f]/30 hover:bg-[#edf5f1]"
                            >
                              <div className="flex items-center gap-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-[#14392f]/10 text-[#14392f]">
                                  <BookOpen className="h-5 w-5" />
                                </div>
                                <div>
                                  <span className="block font-medium text-[#111827]">
                                    {doc.title}
                                  </span>
                                  <span className="text-[13px] text-[#667085] capitalize">
                                    {doc.kind.replace("_", " ")}
                                    {doc.requires_license && !doc.has_license && (
                                      <span className="ml-2 inline-flex items-center gap-1 text-[#ddb737] normal-case">
                                        <Lock className="h-3 w-3" /> Access key needed
                                      </span>
                                    )}
                                  </span>
                                </div>
                              </div>
                              <span className="text-[14px] font-semibold text-[#14392f]">
                                {doc.requires_license && !doc.has_license ? "Unlock" : "Open"}
                              </span>
                            </Link>
                          ))}
                        </div>
                      </div>
                    )}

                  {/* Documents & Files */}
                  {catalogue.resources && catalogue.resources.length > 0 && (
                    <div>
                      <h3 className="mb-3 text-[16px] font-semibold text-[#111827]">
                        Documents & Files
                      </h3>
                      <div className="grid gap-4 md:grid-cols-2">
                        {catalogue.resources.map((resource) => (
                          <div
                            key={resource.id}
                            className="flex items-center justify-between rounded-xl border border-border bg-[#f8faf9] p-4"
                          >
                            <div className="flex items-center gap-3">
                              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-[#14392f]/10 text-[#14392f]">
                                <FileText className="h-5 w-5" />
                              </div>
                              <span className="font-medium text-[#111827]">
                                {resource.resource_title}
                              </span>
                            </div>
                            <a
                              href={resource.resource_file}
                              target="_blank"
                              rel="noreferrer"
                              className="flex h-10 w-10 items-center justify-center rounded-full text-[#14392f] transition-colors hover:bg-neutral-200"
                              title="Download Resource"
                            >
                              <Download className="h-5 w-5" />
                            </a>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* Video Files */}
                  {catalogue.video_files &&
                    catalogue.video_files.length > 0 && (
                      <div>
                        <h3 className="mb-3 text-[16px] font-semibold text-[#111827]">
                          Video Files
                        </h3>
                        <div className="grid grid-cols-3 gap-6">
                          {catalogue.video_files.map((video) => (
                            <CustomVideoPlayer
                              key={video.id}
                              video={video}
                              onComplete={handleVideoEnd}
                            />
                          ))}
                        </div>
                      </div>
                    )}

                  {/* Video Links */}
                  {catalogue.video_links &&
                    catalogue.video_links.length > 0 && (
                      <div>
                        <h3 className="mb-3 text-[16px] font-semibold text-[#111827]">
                          Video Links
                        </h3>
                        <div className="grid grid-cols-3 gap-6">
                          {catalogue.video_links.map((link) => (
                            <CustomYouTubePlayer
                              key={link.id}
                              link={link}
                              onComplete={handleVideoEnd}
                            />
                          ))}
                        </div>
                      </div>
                    )}

                  {/* Live Links */}
                  {catalogue.live_links && catalogue.live_links.length > 0 && (
                    <div>
                      <h3 className="mb-3 text-[16px] font-semibold text-[#111827]">
                        Live Sessions
                      </h3>
                      <div className="grid gap-4 md:grid-cols-2">
                        {catalogue.live_links.map((link) => (
                          <div
                            key={link.id}
                            className="flex items-center justify-between rounded-xl border border-border bg-[#f8faf9] p-4"
                          >
                            <div className="flex items-center gap-3">
                              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-[#14392f]/10 text-[#14392f]">
                                <PlayCircle className="h-5 w-5" />
                              </div>
                              <span className="font-medium text-[#111827]">
                                {link.link_title}
                              </span>
                            </div>
                            <a
                              href={link.link_url}
                              target="_blank"
                              rel="noreferrer"
                              className="flex h-10 w-10 items-center justify-center rounded-full text-[#14392f] transition-colors hover:bg-neutral-200"
                              title="Join Live Session"
                            >
                              <ExternalLink className="h-5 w-5" />
                            </a>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </>
              )}
            </div>
          )}

          {activeTab === "exams" && (
            <div className="space-y-4">
              {catalogue.exams && catalogue.exams.length > 0 ? (
                catalogue.exams.map((exam) => {
                  const hasTaken = !!exam.user_status
                  const isPassed =
                    exam.user_status &&
                    (exam.user_status.status === "passed" ||
                      exam.user_status.status === "Pass" ||
                      exam.user_status.percentage >= 60)
                  const retakeLocked =
                    exam.retake_locked === true ||
                    exam.attempts_exceeded === true ||
                    exam.attempts_count >= exam.max_attempts
                  const retakeEligibleDate = exam.retake_eligible_date
                    ? new Date(exam.retake_eligible_date).toLocaleDateString()
                    : null

                  const hasAnyVideo =
                    (catalogue.video_files &&
                      catalogue.video_files.length > 0) ||
                    (catalogue.video_links && catalogue.video_links.length > 0)
                  const videoFilesCompleted = catalogue.video_files?.length
                    ? catalogue.video_files.every((v) => v.is_completed)
                    : true
                  const videoLinksCompleted = catalogue.video_links?.length
                    ? catalogue.video_links.every((v) => v.is_completed)
                    : true
                  const areAllVideosCompleted =
                    videoFilesCompleted && videoLinksCompleted
                  // The server decides; this local check only drives the hint text.
                  // It used to be the only gate, so opening /dashboard/exams/:id
                  // directly skipped the coursework requirement entirely.
                  const courseworkDone = !hasAnyVideo || areAllVideosCompleted
                  const canTakeExam = exam.can_take_exam !== false && courseworkDone

                  return (
                    <div
                      key={exam.id}
                      className="flex flex-col justify-between gap-4 rounded-xl border border-border bg-[#f8faf9] p-5 md:flex-row md:items-center"
                    >
                      <div>
                        <div className="mb-2 flex items-center gap-3">
                          <h3 className="text-[16px] font-semibold text-[#111827]">
                            {exam.exam_title}
                          </h3>
                          {hasTaken && (
                            <span
                              className={`inline-flex h-6 items-center rounded-full px-3 text-[13px] font-bold tracking-wider uppercase ${isPassed ? "bg-[#d7f8e5] text-[#008a42]" : "bg-[#ffdada] text-[#c62828]"}`}
                            >
                              {isPassed ? "Passed" : "Failed"}
                            </span>
                          )}
                        </div>
                        {hasTaken ? (
                          <div className="flex items-center gap-4 text-[15px] text-[#667085]">
                            <span className="flex items-center gap-1.5">
                              <CheckCircle className="h-4 w-4 text-[#008a42]" />{" "}
                              Score: {exam.user_status?.percentage}%
                            </span>
                            <span className="flex items-center gap-1.5">
                              <Clock className="h-4 w-4" /> Taken:{" "}
                              {new Date(
                                exam.user_status?.taken_at || ""
                              ).toLocaleDateString()}
                            </span>
                          </div>
                        ) : (
                          <p className="text-[15px] text-[#667085]">
                            You haven't taken this exam yet.
                          </p>
                        )}
                        <div className="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-[13px] text-[#667085]">
                          <span>
                            Attempts: {exam.attempts_count} /{" "}
                            {exam.max_attempts}
                          </span>
                          <span>
                            {exam.attempts_exceeded
                              ? "Attempts exceeded"
                              : "Attempts available"}
                          </span>
                          {retakeLocked && (
                            <span className="font-medium text-[#c62828]">
                              Retake locked
                              {retakeEligibleDate
                                ? ` until ${retakeEligibleDate}`
                                : ""}
                            </span>
                          )}
                          {!courseworkDone && (
                            <span className="flex items-center gap-1 font-medium text-[#ddb737]">
                              <AlertCircle className="h-3.5 w-3.5" /> Please
                              complete all videos first
                            </span>
                          )}
                        </div>
                      </div>

                      <div className="flex items-center gap-3">
                        {isPassed && exam.user_status?.download_certificate && (
                          <a
                            href={exam.user_status.download_certificate}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-[#ddb737] px-4 text-[15px] font-semibold text-[#14392f] transition-colors hover:bg-[#c7a431]"
                          >
                            <Download className="h-4 w-4" />
                            Certificate
                          </a>
                        )}
                        {exam.id ? (
                          /* Only the per-user link the server issued is used. The
                             old catalogue-level classmarker_link fallback handed
                             out an ungated exam URL whenever the server withheld
                             exam_link because attempts were exhausted. */
                          exam.exam_link ? (
                            <a
                              href={exam.exam_link}
                              target="_blank"
                              rel="noreferrer"
                              onClick={(event) => {
                                if (retakeLocked || !canTakeExam)
                                  event.preventDefault()
                              }}
                              className={`inline-flex h-10 items-center justify-center gap-2 rounded-lg px-4 text-[15px] font-semibold transition-colors ${retakeLocked || !canTakeExam ? "cursor-not-allowed bg-neutral-200 text-neutral-500" : hasTaken ? "border border-border bg-white text-[#111827] hover:bg-neutral-100" : "bg-[#14392f] text-white hover:bg-[#0f2f26]"}`}
                            >
                              <BookOpen className="h-4 w-4" />
                              {retakeLocked
                                ? "Attempts Exhausted"
                                : hasTaken
                                  ? "Retake Exam"
                                  : "Start Exam"}
                            </a>
                          ) : (
                            <Link
                              to={ROUTES.DASHBOARD_EXAM.replace(
                                ":examId",
                                exam.id.toString()
                              )}
                              aria-disabled={retakeLocked || !canTakeExam}
                              onClick={(event) => {
                                if (retakeLocked || !canTakeExam)
                                  event.preventDefault()
                              }}
                              className={`inline-flex h-10 items-center justify-center gap-2 rounded-lg px-4 text-[15px] font-semibold transition-colors ${retakeLocked || !canTakeExam ? "cursor-not-allowed bg-neutral-200 text-neutral-500" : hasTaken ? "border border-border bg-white text-[#111827] hover:bg-neutral-100" : "bg-[#14392f] text-white hover:bg-[#0f2f26]"}`}
                            >
                              <BookOpen className="h-4 w-4" />
                              {retakeLocked
                                ? "Attempts Exhausted"
                                : hasTaken
                                  ? "Retake Exam"
                                  : "Start Exam"}
                            </Link>
                          )
                        ) : (
                          <span className="inline-flex h-10 items-center justify-center rounded-lg bg-neutral-200 px-4 text-[15px] font-semibold text-neutral-500">
                            Exam unavailable
                          </span>
                        )}
                      </div>
                    </div>
                  )
                })
              ) : (
                <div className="py-12 text-center text-[#667085]">
                  No exams or assessments available for this course.
                </div>
              )}
            </div>
          )}
        </div>
      </div>
    </section>
  )
}
