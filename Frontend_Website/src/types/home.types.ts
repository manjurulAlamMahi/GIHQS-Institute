export interface HomeServicesPathwayItem {
  id: number
  serial: string
  target_audience: string
  title: string
  description: string
  link_text: string | null
}

export interface HomeProfessionalPathwayItem {
  id: number
  serial: string
  title: string
  description: string
  link_text: string | null
}

export interface HomeNextSteps {
  id: number
  title1: string
  title2: string
  tagline: string
  certificate_btn_text: string
  learning_btn_text: string
  advisory_btn_text: string
  member_btn_text: string
}

export interface HomeGihqs {
  id: number
  title1: string
  title2: string
  tagline: string
  description: string
  certificate_btn_text: string
  learning_btn_text: string
  advisory_btn_text: string
  member_btn_text: string
  professional_ecosystem_title: string
  learning_tagline: string
  learning_title: string
  learning_details: string
  certificate_tagline: string
  certificate_title: string
  certificate_details: string
  lead_tagline: string
  lead_title: string
  lead_details: string
  content_file: string | null
  injected_status: boolean
  home_services_pathways: HomeServicesPathwayItem[]
  home_professional_pathways: HomeProfessionalPathwayItem[]
  home_next_steps: HomeNextSteps
}

export interface HomeServicesPathwaysResponse {
  success: boolean
  message: string
  data: {
    home_gihqs: HomeGihqs
  }
  code: number
}

export interface HomeCertificate {
  id: number
  home_recognized_pathway_id: number
  short_title: string
  title: string
  icon: string
  tagline: string
  headline: string
  description: string
  audience: string
  tags: string
  button_text: string
}

export interface HomeRecognizedPathways {
  id: number
  title1: string
  title2: string
  tagline: string | null
  description: string
  content_file: string | null
  injected_status: boolean
  home_certificates: HomeCertificate[]
}

export interface HomeFlagshipCertificationsResponse {
  success: boolean
  message: string
  data: {
    home_recognized_pathways: HomeRecognizedPathways
  }
  code: number
}

export interface PathwayOption {
  id: number
  question_id: number
  option_text: string
  next_question_id: number | null
  result_id: number | null
  order: number
  status: number
}

export interface PathwayQuestionData {
  id: number
  step_number: number
  question_text: string
  status: number
  options: PathwayOption[]
}

export interface PathwayResultData {
  id: number
  title: string
  description: string
  badges: string[]
  info_box_text: string
  primary_button_text: string
  primary_button_url: string
  secondary_button_text?: string
  secondary_button_url?: string
  status: number
}

export interface PathwayStepResponse {
  success: boolean
  type: 'question' | 'result' | string
  data: PathwayQuestionData | PathwayResultData
}


