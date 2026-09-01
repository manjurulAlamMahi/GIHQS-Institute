import { AdvisoryRequestList } from "./components/AdvisoryRequestList"

const AdvisoryRequestsPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <div className="space-y-6">
        <div>
          <h2 className="text-[20px] font-bold text-[#14392f] font-['Outfit']">
            My Advisory Requests
          </h2>
          <p className="mt-1 text-[15px] text-neutral-500">
            View and track the status of your advisory consultation requests.
          </p>
        </div>

        <AdvisoryRequestList />
      </div>
    </section>
  )
}

export default AdvisoryRequestsPage
