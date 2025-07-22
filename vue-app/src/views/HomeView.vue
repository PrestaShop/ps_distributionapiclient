<script setup lang="ts">
import 'vue3-carousel/carousel.css'

import { ref, onMounted } from 'vue'
import HeaderSectionView from '@/views/sections/HeaderSectionView.vue'
import TopSectionView from '@/views/sections/TopSectionView.vue'
import NewContributorsSectionView from '@/views/sections/NewContributorsSectionView.vue'
// import WallOfFameSectionView from '@/views/sections/WallOfFameSectionView.vue';
import ContributeSectionView from '@/views/sections/ContributeSectionView.vue'

const totalContribs = ref<number>(0)
const prestaContribs = ref<number>(0)
const topCompanies = ref<string | null>()
const topContributors = ref()
const newContributors = ref()
const contributorsData = ref()

onMounted(async () => {
  try {
    const response = await fetch('https://contributors.prestashop-project.org/newcontributors.json')
    if (!response.ok) throw new Error('Error loading new contributors')
    const data = await response.json()
    newContributors.value = data
  } catch (error) {
    console.error('Error :', error)
  }

  try {
    const response = await fetch('https://contributors.prestashop-project.org/topcompanies.json')
    if (!response.ok) throw new Error('Error loading top companies')
    const data = await response.json()
    topCompanies.value = data.slice(1, 6)

    const total = data.reduce((acc: number, company: any) => acc + company.contributions, 0)
    totalContribs.value = total

    const presta = data.find((c: any) => c.name.toLowerCase() === 'prestashop')
    prestaContribs.value = presta.contributions
  } catch (error) {
    console.error('Error:', error)
  }

  try {
    const response = await fetch('https://contributors.prestashop-project.org/contributors.json')
    if (!response.ok) throw new Error('Error loading contributors data')
    const data = await response.json()
    contributorsData.value = Object.values(data)
    topContributors.value = contributorsData.value.slice(0, 5)
  } catch (error) {
    console.error('Error :', error)
  }
})
</script>

<template>
  <div class="wof-container">
    <HeaderSectionView :total-contribs="totalContribs" :presta-contribs="prestaContribs" />
    <main>
      <TopSectionView :top-contributors="topContributors" :top-companies="topCompanies" />
      <NewContributorsSectionView :new-contributors="newContributors" />
      <!--
        <WallOfFameSectionView />
      -->
      <ContributeSectionView />
    </main>
  </div>
</template>

<style>
.wof-section {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
@media (min-width: 768px) {
  .wof-section {
    padding: 4rem;
  }
}
.puik-tag p {
  margin-bottom: 0;
}
</style>
