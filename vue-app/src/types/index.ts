
export interface Company {
  name: string
  rank: number
  contributions: number
  avatar_url: string
  html_url: string
}

export interface Contributor {
  login: string
  id: number
  avatar_url: string
  html_url: string
  name: string
  company: string | null
  blog: string | null
  location: string | null
  bio: string | null
  email_domain: string | null
  contributions: number
  repositories: Record<string, number>
  categories: Record<string, {
    total: number
    repositories: Record<string, number>
  }>
}

export interface NewContributor {
  login: string
  name: string
  avatar_url: string
  html_url: string
  contributions: number
  firstContributionAt: string
}
