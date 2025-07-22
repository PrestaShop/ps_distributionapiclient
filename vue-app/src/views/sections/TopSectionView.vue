<script setup lang="ts">
import TopContributorsView from '@/views/sections/sub-sections/TopContributorsView.vue';
import TopCompaniesView from '@/views/sections/sub-sections/TopCompaniesView.vue';

</script>

<template>
  <section class="wof-section wof-top-section">
    <h2 class="puik-h1">PrestaShop Project’s top contributors</h2>
    <div class="wof-top-section__cards">
      <TopCompaniesView />
      <TopContributorsView />
    </div>
  </section>
</template>

<style>
.wof-top-section__cards {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
}
.wof-top-section__card {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  max-width: 100%;
  gap: 0 !important;
}
.wof-top-section__rank {
  width: 1.5rem;
  height: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}
.wof-top-section__rank span {
  line-height: 0;
}
.wof-top-section__rank--first {
  background-color: #ffd999;
}
.wof-top-section__rank--second {
  background-color: #eeeeee;
}
.wof-top-section__rank--third {
  background-color: #e7bd94;
}
</style>
