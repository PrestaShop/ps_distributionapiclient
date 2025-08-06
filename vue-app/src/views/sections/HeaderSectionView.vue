<script setup lang="ts">
defineProps<{
  totalMergedPr: number
  prestaMergedPrByPercent: number
}>()

</script>

<template>
  <header class="wof-section wof-header-section">
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
        <span class="wof-header-section__kpis-value puik-brand-h1">{{ totalMergedPr }}</span>
        <span class="wof-header-section__kpis-label puik-body-default">Total Contributions</span>
      </div>
      <div class="wof-header-section__kpis-item">
        <span class="wof-header-section__kpis-value puik-brand-h1">
          {{ prestaMergedPrByPercent }}%
        </span>
        <span class="wof-header-section__kpis-label puik-body-default">Contributions by PrestaShop</span>
      </div>
      <div class="wof-header-section__kpis-item">
        <span class="wof-header-section__kpis-value puik-brand-h1">
            {{ (100 - prestaMergedPrByPercent).toFixed(2) }}%
        </span>
        <span class="wof-header-section__kpis-label puik-body-default">Contributions by Community</span>
      </div>
    </div>
  </header>
</template>

<style>
:root {
  --wof-header-section-bg: #1d1d1b;
  --wof-header-section-text: #ffffff;
  --wof-description-max-width: 1200px;
}

.wof-header-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  background-color: var(--wof-header-section-bg);
}
.wof-header-section * {
  color: var(--wof-header-section-text);
}
.wof-header-section__title {
  margin-bottom: 0;
  text-align: center;
  text-transform: uppercase;
}
.wof-header-section__description {
  max-width: var(--wof-description-max-width);
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
