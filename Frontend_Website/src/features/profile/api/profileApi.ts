import { baseApi } from '@/lib/baseApi'
import type { 
  OrderHistoryResponse, 
  PurchasedCataloguesListResponse,
  PurchasedCatalogueDetailResponse,
  DashboardStatsResponse,
  AddCeActivityResponse,
  CeActivitiesResponse,
  CeTrackingsResponse,
  CertificationApplicationsResponse,
  CertificationApplicationDetailsResponse,
  AdvisoryRequestsResponse,
  AdvisoryRequestDetailsResponse,
  DashboardOverviewResponse,
  ExamQuestionsResponse, 
  ExamSubmitResponse,
  SubscriptionResponse,
  BaseApiResponse
} from '@/types/profile.types'
import type { CataloguesResponse } from '@/types/catalogue.types'

export const profileApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getDashboardCatalogues: builder.query<CataloguesResponse, { keyword?: string; sorting?: string; filtering?: string } | void>({
      query: (params) => {
        let url = '/catalogues';
        if (params) {
          const searchParams = new URLSearchParams();
          if (params.keyword) searchParams.append('keyword', params.keyword);
          if (params.sorting && params.sorting !== 'all') searchParams.append('sorting', params.sorting);
          if (params.filtering) searchParams.append('filtering', params.filtering);
          
          const queryString = searchParams.toString();
          if (queryString) {
            url += `?${queryString}`;
          }
        }
        return url;
      },
    }),
    getDashboardStats: builder.query<DashboardStatsResponse, void>({
      query: () => '/profile/dashboard-stats',
    }),
    getDashboardOverview: builder.query<DashboardOverviewResponse, void>({
      query: () => '/profile/dashboard-overview',
    }),
    getCertificationApplications: builder.query<CertificationApplicationsResponse, void>({
      query: () => '/get-apply-for-certification',
    }),
    getCertificationApplicationDetails: builder.query<CertificationApplicationDetailsResponse, string | number>({
      query: (id) => `/get-apply-for-certification/${id}`,
    }),
    getAdvisoryRequests: builder.query<AdvisoryRequestsResponse, void>({
      query: () => '/get-advisory-request',
    }),
    getAdvisoryRequestDetails: builder.query<AdvisoryRequestDetailsResponse, string | number>({
      query: (id) => `/get-advisory-request/${id}`,
    }),
    getCeActivities: builder.query<CeActivitiesResponse, void>({
      query: () => '/profile/ce-activities',
    }),
    getCeTrackings: builder.query<CeTrackingsResponse, void>({
      query: () => '/profile/ce-activities/tracking',
    }),
    getOrderHistory: builder.query<OrderHistoryResponse, void>({
      query: () => '/profile/orders',
    }),
    getOrderInvoice: builder.query<Blob, number | string>({
      query: (id) => ({
        url: `/profile/orders/${id}/invoice`,
        responseHandler: (response) => response.blob(),
      }),
    }),
    getPurchasedCatalogues: builder.query<PurchasedCataloguesListResponse, Record<string, string>>({
      query: (params) => ({
        url: '/profile/purchased-catalogues',
        params: params,
      }),
    }),
    getPurchasedCatalogueById: builder.query<PurchasedCatalogueDetailResponse, string | number>({
      query: (id) => `/profile/purchased-catalogues/${id}`,
    }),
    getExamQuestions: builder.query<ExamQuestionsResponse, string | number>({ query: (id) => `/profile/exams/${id}` }),
    submitExam: builder.mutation<ExamSubmitResponse, { id: string | number; answers: { question_id: number; option_id: number }[]; duration: number }>({
      query: ({ id, ...body }) => ({ url: `/profile/exams/${id}/submit`, method: 'POST', body }),
    }),
    completeCatalogueVideo: builder.mutation<
      { success: boolean; message: string; data: any; code: number },
      { video_id?: number; video_link_id?: number; is_completed: boolean }
    >({
      query: (body) => ({
        url: '/profile/purchased-catalogues/videos/complete',
        method: 'POST',
        body,
      }),
    }),
    addCeActivity: builder.mutation<AddCeActivityResponse, FormData>({
      query: (body) => ({
        url: '/profile/ce-activities',
        method: 'POST',
        body,
      }),
    }),
    getSubscription: builder.query<SubscriptionResponse, void>({
      query: () => '/profile/subscription',
    }),
    cancelSubscription: builder.mutation<BaseApiResponse, void>({
      query: () => ({
        url: '/profile/subscription/cancel',
        method: 'POST',
      }),
    }),
    requestRefund: builder.mutation<BaseApiResponse, { order_id: string | number; reason: string }>({
      query: ({ order_id, reason }) => ({
        url: `/profile/orders/${order_id}/request-refund`,
        method: 'POST',
        body: { reason },
      }),
    }),
  }),
})

export const { 
  useGetDashboardCataloguesQuery,
  useGetDashboardStatsQuery,
  useGetDashboardOverviewQuery,
  useGetCertificationApplicationsQuery,
  useGetCertificationApplicationDetailsQuery,
  useGetAdvisoryRequestsQuery,
  useGetAdvisoryRequestDetailsQuery,
  useGetCeActivitiesQuery,
  useGetCeTrackingsQuery,
  useGetOrderHistoryQuery, 
  useLazyGetOrderInvoiceQuery,
  useGetPurchasedCataloguesQuery,
  useGetPurchasedCatalogueByIdQuery,
  useGetExamQuestionsQuery,
  useSubmitExamMutation,
  useCompleteCatalogueVideoMutation,
  useAddCeActivityMutation,
  useGetSubscriptionQuery,
  useCancelSubscriptionMutation,
  useRequestRefundMutation
} = profileApi
