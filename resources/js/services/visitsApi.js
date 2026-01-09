export async function fetchVisits() {
    const res = await fetch('/api/visits', { headers: { Accept: 'application/json' } })
    if (!res.ok) throw new Error(`API error: ${res.status}`)
    const json = await res.json()
    return json.data ?? []
  }
  