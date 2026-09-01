import { baseApi } from '@/lib/baseApi'
import type { 
  AccreditationHeaderResponse,
  AccreditationDetailsResponse,
  AccreditationFeesResponse,
  AccreditationApplyHeroResponse,
  ApplyAccreditationResponse,
  GetAccreditationApplicationsResponse,
  GetAccreditationApplicationDetailsResponse
} from '@/types/accreditation.types'

export const accreditationApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getAccreditationApplicationDetails: builder.query<GetAccreditationApplicationDetailsResponse, number>({
      query: (id) => `/apply-accreditation/${id}`,
    }),
    getAccreditationApplications: builder.query<GetAccreditationApplicationsResponse, void>({
      query: () => '/apply-accreditation',
    }),
    getAccreditationHeader: builder.query<AccreditationHeaderResponse, void>({
      query: () => '/accreditation-header',
    }),
    getAccreditationDetails: builder.query<AccreditationDetailsResponse, void>({
      query: () => '/accreditation-details',
    }),
    getAccreditationFees: builder.query<AccreditationFeesResponse, void>({
      query: () => '/accreditation-fees',
    }),
    getAccreditationApplyHero: builder.query<AccreditationApplyHeroResponse, void>({
      query: () => '/accreditation-apply-hero',
    }),
    applyAccreditation: builder.mutation<ApplyAccreditationResponse, FormData>({
      query: (body) => ({
        url: '/apply-accreditation',
        method: 'POST',
        body,
      }),
    }),
  }),
})

export const { 
  useGetAccreditationApplicationDetailsQuery,
  useGetAccreditationApplicationsQuery,
  useGetAccreditationHeaderQuery,
  useGetAccreditationDetailsQuery,
  useGetAccreditationFeesQuery,
  useGetAccreditationApplyHeroQuery,
  useApplyAccreditationMutation
} = accreditationApi
