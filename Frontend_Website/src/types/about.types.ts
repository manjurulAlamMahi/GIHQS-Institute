export interface Faq {
  id: number
  faq_title: string
  faq_short_description: string
}

export interface AboutInstituteData {
  id: number
  title1: string
  title2: string
  tag_line: string
  description: string
  image: string
  content_file: string | null
  injected_status: boolean
  faqs: Faq[]
}

export interface AboutInstituteResponse {
  success: boolean
  message: string
  data: {
    about_institutes: AboutInstituteData
  }
  code: number
}

export interface AboutContactData {
  id: number
  title: string
  phone: string
  email: string
  address: string
  working_hours: string
  mission: string
  content_file: string | null
  injected_status: boolean
}

export interface AboutContactResponse {
  success: boolean
  message: string
  data: {
    about_contact: AboutContactData
  }
  code: number
}

export interface VisionMissionValuesData {
  id: number
  tagline: string
  title1: string
  title2: string
  short_description: string
  vision_tagline: string
  vision_title: string
  vision_short_description: string
  mission_tagline: string
  mission_title: string
  mission_short_description: string
  value_tagline: string
  value_title: string
  value_title2: string
  value_short_description: string
  global_perspective_tagline: string
  global_perspective_title: string
  global_perspective_short_description: string
  integrity_tagline: string
  integrity_title: string
  integrity_short_description: string
  human_centered_tagline: string
  human_centered_title: string
  human_centered_short_description: string
  quality_excellence_tagline: string
  quality_excellence_title: string
  quality_excellence_short_description: string
  safety_leadership_tagline: string
  safety_leadership_title: string
  safety_leadership_short_description: string
  content_file: string | null
  injected_status: boolean
}

export interface VisionMissionValuesResponse {
  success: boolean
  message: string
  data: {
    vision_mission_values: VisionMissionValuesData
  }
  code: number
}

export interface PoliciesGovernanceDocument {
  id: number
  title: string
  file: string | null
}

export interface PoliciesGovernanceData {
  id: number
  title1: string
  title2: string
  tagline: string
  description: string
  inst_title: string
  inst_tag: string
  inst_description: string
  institutional_documents: PoliciesGovernanceDocument[]
  cert_title: string
  cert_tag: string
  cert_description: string
  certification_documents: PoliciesGovernanceDocument[]
  acc_title: string
  acc_tag: string
  acc_description: string
  accreditation_documents: PoliciesGovernanceDocument[]
  commitment_title1: string
  commitment_title2: string
  commitment_description: string
  content_file: string | null
  injected_status: boolean
}

export interface PoliciesGovernanceResponse {
  success: boolean
  message: string
  data: {
    policies_governances: PoliciesGovernanceData
  }
  code: number
}

export interface StrategicAdvisoryFeature {
  id: number
  description: string
}

export interface StrategicAdvisoryData {
  id: number
  title1: string
  title2: string
  tagline: string
  short_description: string
  purpose_tagline: string
  purpose_title: string
  purpose_short_description: string
  advisory_title: string
  panel_title: string
  panel_short_description: string
  appointment_title: string
  appointment_short_description: string
  conflict_title: string
  conflict_short_description: string
  expression_title: string
  expression_description: string
  content_file: string | null
  injected_status: boolean
  strategic_advisory_features: StrategicAdvisoryFeature[]
}

export interface StrategicAdvisoryResponse {
  success: boolean
  message: string
  data: {
    strategic_advisories: StrategicAdvisoryData
  }
  code: number
}

export interface ContactMessagePayload {
  first_name: string
  last_name: string
  email: string
  phone: string
  organization: string
  service_of_interest: string
  message: string
}

export interface ContactMessageResponse {
  success: boolean
  message: string
  data: ContactMessagePayload & {
    status: string
    updated_at: string
    created_at: string
    id: number
  }
  code: number
}

export interface AccreditationReviewFeature {
  id: number
  description: string
}

export interface AccreditationReviewData {
  id: number
  title1: string
  title2: string
  tagline: string
  short_description: string
  purpose_tagline: string
  purpose_title: string
  purpose_short_description: string
  review_title: string
  panel_title: string
  panel_short_description: string
  appointment_title: string
  appointment_short_description: string
  conflict_title: string
  conflict_short_description: string
  expression_title: string
  expression_description: string
  content_file: string | null
  injected_status: boolean
  accreditation_review_features: AccreditationReviewFeature[]
}

export interface AccreditationReviewResponse {
  success: boolean
  message: string
  data: {
    accreditation_reviews: AccreditationReviewData
  }
  code: number
}

export interface WebsiteSettingData {
  id: number
  logo: string | null
  favicon: string | null
  company_name: string
  tag_line: string
  phone_number: string
  whatsapp_number: string
  primary_email: string
  support_email: string
  company_address: string
  copyright_text: string
}

export interface WebsiteSettingResponse {
  success: boolean
  message: string
  data: {
    website_setting: WebsiteSettingData
  }
  code: number
}

export interface OtherPageData {
  id: number
  slug: string
  title: string
  content_file: string | null
  injected_status: boolean
}

export interface OtherPageResponse {
  success: boolean
  message: string
  data: {
    other_page: OtherPageData
  }
  code: number
}


