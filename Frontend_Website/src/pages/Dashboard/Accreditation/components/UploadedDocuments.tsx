const documents = [
  {
    document: "Program Policy Manual.pdf",
    category: "Policy",
    uploaded: "Feb 11, 2026",
    status: "Accepted",
    statusClass: "bg-[#dcfce7] text-[#16a34a]",
    notes: "Meets documentation requirements.",
    action: "View",
  },
  {
    document: "Faculty CV Packet.pdf",
    category: "Faculty Evidence",
    uploaded: "Feb 11, 2026",
    status: "Revision Needed",
    statusClass: "bg-[#fff5e8] text-[#f97316]",
    notes: "Updated qualifications and dates requested.",
    action: "Replace",
  },
  {
    document: "Assessment Mapping.xlsx",
    category: "Assessment",
    uploaded: "Feb 12, 2026",
    status: "Revision Needed",
    statusClass: "bg-[#fff5e8] text-[#f97316]",
    notes: "Domain 3 mapping lacks scoring explanation.",
    action: "Replace",
  },
]

export function UploadedDocuments() {
  return (
    <section className="rounded-[10px] border border-border bg-white p-6 shadow-sm">
      <h2 className="text-[16px] font-semibold text-[#111827]">Uploaded Documents</h2>
      <p className="mt-1 text-[13px] text-muted-foreground">
        Submitted files and current review status.
      </p>

      <div className="mt-6 overflow-x-auto">
        <table className="w-full min-w-215 border-collapse text-left">
          <thead>
            <tr className="text-[12px] uppercase text-[#667085]">
              <th className="py-3 font-medium">Document</th>
              <th className="py-3 font-medium">Category</th>
              <th className="py-3 font-medium">Uploaded</th>
              <th className="py-3 font-medium">Status</th>
              <th className="py-3 font-medium">Reviewer Note</th>
              <th className="py-3 text-right font-medium">Action</th>
            </tr>
          </thead>
          <tbody>
            {documents.map((item) => (
              <tr key={item.document} className="text-[13px] text-[#344054]">
                <td className="py-3 font-medium">{item.document}</td>
                <td className="py-3">{item.category}</td>
                <td className="py-3">{item.uploaded}</td>
                <td className="py-3">
                  <span
                    className={`inline-flex h-6 items-center rounded-full px-3 text-[12px] font-medium ${item.statusClass}`}
                  >
                    {item.status}
                  </span>
                </td>
                <td className="py-3 text-[#17806b]">{item.notes}</td>
                <td className="py-3 text-right">
                  <button className="text-[13px] font-semibold text-[#14392f] hover:underline">
                    {item.action}
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  )
}
