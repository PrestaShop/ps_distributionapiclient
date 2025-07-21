<script setup lang="ts">

import { ref, onMounted } from 'vue'

const topCompanies = ref<string | null>()
const totalContribs = ref()
const prestaContribs = ref()

onMounted(async () => {
  try {
    const response = await fetch('https://contributors.prestashop-project.org/topcompanies.json')
    if (!response.ok) throw new Error('Error loading top companies')
    const data = await response.json()
    topCompanies.value = data

    const total = data.reduce((acc: number, company: any) => acc + company.contributions, 0)
    totalContribs.value = total

    const presta = data.find((c: any) => c.name.toLowerCase() === 'prestashop')
    prestaContribs.value = presta.contributions

  } catch (error) {
    console.error('Error loading top companies:', error)
  }
})
</script>

<template>
  <header class="wof-header">
    <h1 class="wof-header-title puik-brand-jumbotron">MEET our community Heroes</h1>
    <p class="wof-header-description puik-body-default">
      From day one, PrestaShop has thrived as an open-source platform powered by a talented
      community of developers, merchants, and contributors. We all work together to improve and
      support the scalability of the PrestaShop e-commerce platform. By remaining the main
      contributors to its development, PrestaShop ensures long-term sustainability for everyone in
      the ecosystem. The project grows with each contribution, and with each contribution our
      contributors’ expertise grows. Take a look at our community.
    </p>
    <div class="wof-header-kpis__container">
      <div class="wof-header-kpis__item">
        <span class="wof-header-kpis__value puik-brand-h1">{{ totalContribs }}</span>
        <span class="wof-header-kpis__label puik-body-default">Total Contributions</span>
      </div>
      <div class="wof-header-kpis__item">
        <span class="wof-header-kpis__value puik-brand-h1">
          {{ Math.round((prestaContribs / totalContribs) * 100) }}%
        </span>
        <span class="wof-header-kpis__label puik-body-default">Contribs by PrestaShop</span>
      </div>
      <div class="wof-header-kpis__item">
        <span class="wof-header-kpis__value puik-brand-h1">
          {{ Math.round(100 - (prestaContribs / totalContribs) * 100) }}%
        </span>
        <span class="wof-header-kpis__label puik-body-default">Contribs by Community</span>
      </div>
    </div>
  </header>
</template>

<style>
.wof-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  padding: 1rem;
  background-color: #1d1d1b;
}
@media (min-width: 768px) {
  .wof-header {
    padding: 4rem;
  }
}
.wof-header-title {
  text-align: center;
  text-transform: uppercase;
}
.wof-header-description {
  text-align: center;
}
.wof-header * {
  color: white;
}
.wof-header-kpis__container {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 2rem;
}
.wof-header-kpis__item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 190px;
}
</style>
