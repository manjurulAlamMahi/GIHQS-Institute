import { Upload } from "lucide-react"

export default function AttachmentCard({
  title,
  help,
  error,
  onChange,
  fileName
}: {
  title: string
  help: string
  error?: string
  onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void
  fileName?: string
}) {
  return (
    <div className="rounded-2xl border border-[#d7e1de] bg-[#fbfdfc] p-4 flex flex-col h-full">
      <p className="mb-3 text-sm font-bold text-[#0F2F26]">{title}</p>
      <label className="flex min-h-32 cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-[#cbd8d4] bg-white text-center transition-colors hover:border-[#C7A44D] p-4 grow">
        <Upload className="mb-3 h-5 w-5 text-[#5d756f]" />
        <span className="text-sm text-[#5d756f]">
          {fileName ? (
            <span className="font-semibold text-[#0F2F26]">{fileName}</span>
          ) : (
            <><span className="font-semibold underline">Click to upload</span> or drag and drop</>
          )}
        </span>
        <input 
          type="file" 
          className="sr-only" 
          accept=".pdf,.doc,.docx"
          onChange={onChange} 
        />
      </label>
      {error && <span className="text-xs text-red-500 mt-2">{error}</span>}
      <p className="mt-3 text-xs leading-relaxed text-[#6b817b]">{help}</p>
    </div>
  )
}
