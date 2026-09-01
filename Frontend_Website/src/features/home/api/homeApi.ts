import { baseApi } from '@/lib/baseApi'
import type { 
  HomeServicesPathwaysResponse,
  HomeFlagshipCertificationsResponse,
  PathwayStepResponse
} from '@/types/home.types'

export const homeApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getHomeServicesPathways: builder.query<HomeServicesPathwaysResponse, void>({
      query: () => '/home-services-pathways',
    }),
    getHomeFlagshipCertifications: builder.query<HomeFlagshipCertificationsResponse, void>({
      query: () => '/home-flagship-certifications',
    }),
    getPathwaysStart: builder.query<PathwayStepResponse, void>({
      query: () => '/pathways/start',
    }),
    getPathwayStep: builder.query<PathwayStepResponse, number | string>({
      query: (optionId) => `/pathways/step/${optionId}`,
    }),
  }),
})

export const { 
  useGetHomeServicesPathwaysQuery,
  useGetHomeFlagshipCertificationsQuery,
  useGetPathwaysStartQuery,
  useLazyGetPathwayStepQuery,
} = homeApi

