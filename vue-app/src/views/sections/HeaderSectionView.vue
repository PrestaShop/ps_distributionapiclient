<script setup lang="ts">
defineProps<{
  totalContribs: number
  prestaContribs: number
}>()

</script>

<template>
  <header class="wof-header-section">
    <h1 class="wof-header-section__title puik-brand-jumbotron">MEET our community Heroes</h1>
    <p class="wof-header-section__description puik-body-default">
      From day one, PrestaShop has thrived as an open-source platform powered by a talented
      community of developers, merchants, and contributors. We all work together to improve and
      support the scalability of the PrestaShop e-commerce platform. By remaining the main
      contributors to its development, PrestaShop ensures long-term sustainability for everyone in
      the ecosystem. The project grows with each contribution, and with each contribution our
      contributors’ expertise grows. Take a look at our community.
    </p>
    <div class="wof-header-section__kpis-container">
      <div class="wof-header-section__kpis-item">
        <span class="wof-header-section__kpis-value puik-brand-h1">{{ totalContribs }}</span>
        <span class="wof-header-section__kpis-label puik-body-default">Total Contributions</span>
      </div>
      <div class="wof-header-section__kpis-item">
        <span class="wof-header-section__kpis-value puik-brand-h1">
          {{ Math.round((prestaContribs / totalContribs) * 100) }}%
        </span>
        <span class="wof-header-section__kpis-label puik-body-default">Contribs by PrestaShop</span>
      </div>
      <div class="wof-header-section__kpis-item">
        <span class="wof-header-section__kpis-value puik-brand-h1">
          {{ Math.round(100 - (prestaContribs / totalContribs) * 100) }}%
        </span>
        <span class="wof-header-section__kpis-label puik-body-default">Contribs by Community</span>
      </div>
    </div>
  </header>
</template>

<style>
.wof-header-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  padding: 1rem;
  background-color: #1d1d1b;
}
@media (min-width: 768px) {
  .wof-header-section {
    padding: 4rem;
  }
}
.wof-header-section * {
  color: white;
}
.wof-header-section__title {
  text-align: center;
  text-transform: uppercase;
}
.wof-header-section__description {
  text-align: center;
}
.wof-header-section__kpis-container {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 2rem;
}
.wof-header-section__kpis-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 190px;
}
</style>
