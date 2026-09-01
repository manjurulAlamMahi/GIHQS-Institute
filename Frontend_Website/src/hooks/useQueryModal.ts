
import { useCallback } from "react"
import { useLocation, useNavigate, useSearchParams } from "react-router"

export function useQueryModal(key: string, value: string = "true") {
  const [searchParams] = useSearchParams()
  const { pathname } = useLocation()
  const navigate = useNavigate()

  const currentValue = searchParams.get(key)
  const isOpen = currentValue === value

  const open = useCallback(
    (nextValue?: string) => {
      const params = new URLSearchParams(searchParams)
      const resolvedValue = nextValue ?? value
      params.set(key, resolvedValue)

      navigate(
        {
          pathname,
          search: params.toString(),
        },
        { preventScrollReset: true }
      )
    },
    [key, value, pathname, navigate, searchParams]
  )

  const close = useCallback(() => {
    const params = new URLSearchParams(searchParams)
    params.delete(key)

    navigate(
      {
        pathname,
        search: params.toString(),
      },
      { preventScrollReset: true }
    )
  }, [key, pathname, navigate, searchParams])

  const toggle = useCallback(() => {
    if (isOpen) {
      close()
    } else {
      open()
    }
  }, [isOpen, close, open])

  return {
    currentValue,
    isOpen,
    open,
    close,
    toggle,
  }
}
