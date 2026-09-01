import { useState } from "react"
import { CertificationList } from "./components/CertificationList"
import { CertificationToolbar } from "./components/CertificationToolbar"
import { ApplicationList } from "./components/ApplicationList"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"

const MyCertificationsPage = () => {
  const [search, setSearch] = useState("")
  const [status, setStatus] = useState("all")

  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <div className="space-y-7">
        <Tabs defaultValue="certifications" className="w-full">
          <TabsList className="mb-6 bg-white border border-border rounded-[10px] p-1 h-12 inline-flex">
            <TabsTrigger 
              value="certifications" 
              className="rounded-md px-6 py-2 text-[15px] font-medium data-[state=active]:bg-[#14392f] data-[state=active]:text-white"
            >
              My Certifications
            </TabsTrigger>
            <TabsTrigger 
              value="applications" 
              className="rounded-md px-6 py-2 text-[15px] font-medium data-[state=active]:bg-[#14392f] data-[state=active]:text-white"
            >
              My Applications
            </TabsTrigger>
          </TabsList>

          <TabsContent value="certifications" className="mt-0 outline-none">
            <div className="rounded-[10px] bg-white p-7 shadow-sm">
              <CertificationToolbar
                search={search}
                status={status}
                onSearchChange={setSearch}
                onStatusChange={setStatus}
              />
              <div className="mt-10">
                <CertificationList search={search} status={status} />
              </div>
            </div>
          </TabsContent>

          <TabsContent value="applications" className="mt-0 outline-none">
            <div className="rounded-[10px] bg-white p-7 shadow-sm">
              <div className="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                  <h2 className="text-xl font-serif text-[#14392f]">Certification Applications</h2>
                  <p className="text-sm text-neutral-500 mt-1">Track the status of your submitted applications.</p>
                </div>
              </div>
              <ApplicationList />
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </section>
  )
}

export default MyCertificationsPage
