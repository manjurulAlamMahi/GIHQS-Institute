import { baseApi } from '@/lib/baseApi'
import type { 
  AboutInstituteResponse, 
  AboutContactResponse, 
  VisionMissionValuesResponse, 
  PoliciesGovernanceResponse, 
  StrategicAdvisoryResponse, 
  AccreditationReviewResponse,
  ContactMessagePayload,
  ContactMessageResponse,
  WebsiteSettingResponse,
  OtherPageResponse
} from '@/types/about.types'

export const aboutApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getAboutInstitute: builder.query<AboutInstituteResponse, void>({
      query: () => '/about-institute',
    }),
    getAboutContact: builder.query<AboutContactResponse, void>({
      query: () => '/about-contact',
    }),
    getVisionMissionValues: builder.query<VisionMissionValuesResponse, void>({
      query: () => '/vision-mission-values',
    }),
    getPoliciesGovernance: builder.query<PoliciesGovernanceResponse, void>({
      query: () => '/policies-governance',
    }),
    getStrategicAdvisory: builder.query<StrategicAdvisoryResponse, void>({
      query: () => '/strategic-advisory',
    }),
    getAccreditationReview: builder.query<AccreditationReviewResponse, void>({
      query: () => '/accreditation-review',
    }),
    submitContactMessage: builder.mutation<ContactMessageResponse, ContactMessagePayload>({
      query: (body) => ({
        url: '/about-contact-message',
        method: 'POST',
        body,
      }),
    }),
    getWebsiteSetting: builder.query<WebsiteSettingResponse, void>({
      query: () => '/website-setting',
    }),
    getOtherPage: builder.query<OtherPageResponse, string>({
      query: (slug) => `/other-pages?slug=${slug}`,
    }),
  }),
})

export const { 
  useGetAboutInstituteQuery,
  useGetAboutContactQuery,
  useGetVisionMissionValuesQuery,
  useGetPoliciesGovernanceQuery,
  useGetStrategicAdvisoryQuery,
  useGetAccreditationReviewQuery,
  useSubmitContactMessageMutation,
  useGetWebsiteSettingQuery,
  useGetOtherPageQuery
} = aboutApi
