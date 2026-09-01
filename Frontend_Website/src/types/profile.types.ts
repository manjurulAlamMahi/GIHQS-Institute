export interface Order {
  id: number
  order_id: string
  item: string
  date: string
  amount: string
  raw_amount: number
  method: string
  status: string
  type: string
  invoice_url: string
}

export interface OrderHistoryResponse {
  success: boolean
  message: string
  data: {
    order_history: Order[]
  }
  code: number
}

export interface PurchasedCatalogueFeature {
  id: number
  catalogue_id: number
  description: string
}

export interface PurchasedCatalogue {
  id: number
  title: string
  short_title: string
  short_description: string
  price_regular: number
  price_member: number
  service_type: string
  details_file: string
  module_file: string
  is_feature: boolean
  is_trending: boolean
  is_popular: boolean
  healthcare_quality_improvement: boolean
  patient_safety_risk_management: boolean
  status: number
  /**
   * Exam links are issued per user, per attempt, by the server (exam.exam_link).
   * A catalogue-level ClassMarker link is deliberately not part of this contract:
   * it bypassed the attempt, cooldown and coursework checks.
   */
  coursework_completed?: boolean
  features: PurchasedCatalogueFeature[]
}

export interface PurchasedCataloguesListResponse {
  success: boolean
  message: string
  data: {
    catalogues: PurchasedCatalogue[]
  }
  code: number
}

export interface PurchasedCatalogueResource {
  id: number
  catalogue_id: number
  resource_title: string
  resource_file: string
  is_premium: boolean
}

export interface PurchasedCatalogueUserStatus {
  status: string | null
  score: number
  percentage: number
  taken_at: string
  certificate_serial_number: string | null
  certificate_url: string | null
  download_certificate: string | null
  view_results_url: string | null
}

export interface PurchasedCatalogueExam {
  id: number
  catalogue_id: number
  exam_title: string
  exam_link: string | null
  /** Server's verdict on whether this exam may be started right now. */
  can_take_exam: boolean
  exam_type?: "classmarker" | "local" | null
  local_exam_id?: number | null
  is_premium: boolean
  max_attempts: number
  attempts_count: number
  attempts_exceeded: boolean
  retake_locked: boolean
  retake_eligible_date: string | null
  user_status: PurchasedCatalogueUserStatus | null
}

export interface PurchasedCatalogueLiveLink {
  id: number
  catalogue_id: number
  link_title: string
  link_url: string
}

export interface PurchasedCatalogueVideoFile {
  id: number
  catalogue_id: number
  video_title: string
  video_file: string
  is_completed: boolean
  thumbnail?: string | null
}

export interface PurchasedCatalogueVideoLink {
  id: number
  catalogue_id: number
  video_link_title: string
  video_link_url: string
  is_completed: boolean
}

export interface PurchasedCatalogueDetail extends PurchasedCatalogue {
  resources: PurchasedCatalogueResource[]
  live_links?: PurchasedCatalogueLiveLink[]
  video_files?: PurchasedCatalogueVideoFile[]
  video_links?: PurchasedCatalogueVideoLink[]
  exams: PurchasedCatalogueExam[]
  overview_video?: string | null
}

export interface PurchasedCatalogueDetailResponse {
  success: boolean
  message: string
  data: {
    catalogue: PurchasedCatalogueDetail
  }
  code: number
}

export interface ExamQuestionOption {
  id: number
  option_text: string
  sort_order: number
}
export interface ExamQuestion {
  id: number
  question_text: string
  sort_order: number
  options: ExamQuestionOption[]
}
export interface ExamQuestionsResponse {
  success: boolean
  message: string
  code: number
  data: {
    exam: {
      id: number
      catalogue_id: number
      exam_title: string
      questions: ExamQuestion[]
    }
  }
}
export interface ExamSubmitResponse {
  success: boolean
  message: string
  code: number
  data: {
    result: {
      id: number
      score: number
      points_available: number
      percentage: number
      percentage_passmark: number
      status: string
      taken_at: string
      duration: string
      certificate_serial_number: string | null
      certificate_url: string | null
      download_certificate: string | null
    }
  }
}

export interface DashboardStats {
  active_courses: number
  completed_courses: number
  exams_pending: number
  ce_eligible_courses: number
  [key: string]: number // Allow future dynamic keys
}

export interface DashboardStatsResponse {
  success: boolean
  message: string
  data: {
    stats: DashboardStats
  }
  code: number
}

export interface CeActivity {
  id: number
  catalogue_id: string
  certification: string
  certification_short: string
  domain: string
  activity_type: string
  activity_title: string
  provider: string
  completion_date: string
  credits_earned: number
  evidence_file: string | null
  description?: string
  status: string
  created_at: string
}

export interface CertificationApplication {
  id: number
  reference_number: string
  catalogue_id: number
  certification_title: string
  first_name: string
  last_name: string
  applicant_name: string
  email: string
  phone: string
  organization: string
  status: string
  admin_notes: string
  submission_date: string
  created_at: string
}

export interface CertificationApplicationDetails extends CertificationApplication {
  country: string
  city: string
  current_job_title: string
  linkedin_profile: string
  years_of_experience: string
  primary_area_of_experience: string
  professional_role: string
  resume_cv: string
  confirm_accuracy: boolean
  agree_policies: boolean
  updated_at: string
}

export interface CertificationApplicationsResponse {
  success: boolean
  message: string
  data: {
    applications: CertificationApplication[]
  }
  code: number
}

export interface CertificationApplicationDetailsResponse {
  success: boolean
  message: string
  data: {
    application: CertificationApplicationDetails
  }
  code: number
}

export interface AdvisoryRequest {
  id: number
  reference_number: string
  organization_name: string
  full_name: string
  work_email: string
  phone_number: string
  country: string
  organization_type: string
  service_of_interest: string
  desired_timeline: string
  status: string
  admin_notes: string
  submission_date: string
  created_at: string
}

export interface AdvisoryRequestDetails extends AdvisoryRequest {
  description_of_needs: string
  updated_at: string
}

export interface AdvisoryRequestsResponse {
  success: boolean
  message: string
  data: {
    advisory_requests: AdvisoryRequest[]
  }
  code: number
}

export interface AdvisoryRequestDetailsResponse {
  success: boolean
  message: string
  data: {
    advisory_request: AdvisoryRequestDetails
  }
  code: number
}

export interface AddCeActivityResponse {
  success: boolean
  message: string
  data: {
    activity: CeActivity
  }
  code: number
}

export interface DashboardOverviewCertification {
  id: number
  reference_number: string
  catalogue_id: number
  title: string
  status: string
  applied_date: string
  action_text: string
}

export interface DashboardOverviewCourse {
  id: number
  title: string
  service_type: string
  progress_percentage: number
  status_label: string
  action_text: string
}

export interface DashboardOverviewAccreditation {
  id: number
  reference_number: string
  program_name: string
  status: string
  admin_notes: string | null
  submission_date: string
}

export interface DashboardOverviewResponse {
  success: boolean
  message: string
  data: {
    certification: DashboardOverviewCertification | null
    course: DashboardOverviewCourse | null
    accreditation: DashboardOverviewAccreditation | null
  }
  code: number
}

export interface CeActivitiesResponse {
  success: boolean
  message: string
  data: {
    activities: CeActivity[]
  }
  code: number
}

export interface CeTracking {
  catalogue_id: number
  certification_title: string
  certification_short: string
  required_credits: number
  completed_credits: number
  renewal_date: string
  expiration_date: string
  ce_window: string
  submission_due: string
}

export interface CeTrackingsResponse {
  success: boolean
  message: string
  data: {
    trackings: CeTracking[]
  }
  code: number
}

export interface SubscriptionPackage {
  id: number
  name: string
  title: string
  price: number
}

export interface Subscription {
  subscription_id: string
  status: string
  period_start: string | null
  period_end: string | null
  next_renewal_date: string | null
  cancel_at_period_end: boolean
  package: SubscriptionPackage
}

export interface SubscriptionResponse {
  success: boolean
  message: string
  data: {
    has_active_subscription: boolean
    subscription: Subscription | null
  }
  code: number
}

export interface BaseApiResponse {
  success: boolean
  message: string
  code: number
  data?: unknown
}
