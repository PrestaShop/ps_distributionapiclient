<script setup lang="ts">
import 'vue3-carousel/carousel.css'

import { ref, onMounted } from 'vue'
import HeaderSectionView from '@/views/sections/HeaderSectionView.vue'
import TopSectionView from '@/views/sections/TopSectionView.vue'
import NewContributorsSectionView from '@/views/sections/NewContributorsSectionView.vue'
import ContributeSectionView from '@/views/sections/ContributeSectionView.vue'
import type { Company, Contributor, NewContributor } from '@/types'

const totalMergedPr = ref<number>(0)
const prestaMergedPrbyPercent = ref<number>(0)
const topCompanies = ref<Company[]>([])
const contributorsData = ref<Contributor[]>([])
const topContributors = ref<Contributor[]>([])
const newContributors = ref<NewContributor[]>([])

onMounted(async () => {
  try {
    const response = await fetch('https://contributors.prestashop-project.org/newcontributors.json')
    if (!response.ok) throw new Error('Error loading new contributors')
    const data: Record<string, NewContributor> = await response.json()
    newContributors.value = Object.values(data)
  } catch (error) {
    console.error('Error loading new contributors:', error)
  }

  try {
    const response = await fetch('https://contributors.prestashop-project.org/topcompanies.json')
    if (!response.ok) throw new Error('Error loading top companies')
    const data = await response.json()
    topCompanies.value = data.companies.slice(0, 5)

    const total: number =
      data.companies.reduce((acc: number, company: Company) => acc + company.merged_pull_requests, 0)
      + data.community.merged_pull_requests
    totalMergedPr.value = total ?? 0

    const prestashopCompany = data.companies.find((company: Company) => company.name === 'PrestaShop')
    prestaMergedPrbyPercent.value = prestashopCompany.pull_requests_percent ?? 0

  } catch (error) {
    console.error('Error loading top companies:', error)
  }

  try {
    const response = await fetch('https://contributors.prestashop-project.org/contributors_prs.json')
    if (!response.ok) throw new Error('Error loading contributors data')

    const data = await response.json()

    // Filter out non-contributor entries and nulls (e.g., "updatedAt") from the JSON object
    const contributorsOnly = Object.values(data).filter(
      (item): item is Contributor =>
        item !== null && typeof item === 'object' && 'contributions' in item,
    )
    contributorsData.value = contributorsOnly
    topContributors.value = contributorsOnly.slice(0, 5)

  } catch (error) {
    console.error('Error loading contributors data:', error)
  }
})
</script>

<template>
  <div class="wof-container">
    <HeaderSectionView
      :total-merged-pr="totalMergedPr"
      :presta-merged-pr-by-percent="prestaMergedPrbyPercent"
    />
    <main>
      <TopSectionView :top-contributors="topContributors" :top-companies="topCompanies" />
      <NewContributorsSectionView :new-contributors="newContributors" />
      <ContributeSectionView
        contributeLink="https://devdocs.prestashop-project.org/9/contribute/contribute-pull-requests/"
        slackLink="https://www.prestashop-project.org/slack/"
      />
    </main>
  </div>
</template>

<style>
:root {
  --wof-section-gap: 1.5rem;
  --wof-section-padding: 2.5rem 1rem;
  --wof-section-padding-lg: 4rem;
  --wof-avatar-bg: #fff;
  --wof-jumbotron-size-sm: 2.5rem;
  --wof-h1-size-sm: 1.75rem;
}

.wof-section {
  padding: var(--wof-section-padding);
  display: flex;
  flex-direction: column;
  gap: var(--wof-section-gap);
}
@media (min-width: 768px) {
  .wof-section {
    padding: var(--wof-section-padding-lg);
  }
}
.puik-tag p {
  margin-bottom: 0;
}
a.puik-button:hover {
  text-decoration: none;
}
.puik-avatar.puik-avatar--photo {
  background-color: var(--wof-avatar-bg);
}
@media (max-width: 768px) {
  .puik-brand-jumbotron {
    font-size: var(--wof-jumbotron-size-sm);
  }
  .puik-h1 {
    font-size: var(--wof-h1-size-sm);
  }
}
</style>
