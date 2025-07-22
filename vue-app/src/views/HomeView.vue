<script setup lang="ts">
import 'vue3-carousel/carousel.css'

// import { ref, onMounted } from 'vue'
import HeaderSectionView from '@/views/sections/HeaderSectionView.vue';
import TopSectionView from '@/views/sections/TopSectionView.vue';
import NewContributorsSectionView from '@/views/sections/NewContributorsSectionView.vue';
import WallOfFameSectionView from '@/views/sections/WallOfFameSectionView.vue';
import ContributeSectionView from '@/views/sections/ContributeSectionView.vue';

// onMounted(async () => {
// })
</script>

<template>
  <div class="wof-container">
    <HeaderSectionView />
    <main>
      <TopSectionView />
      <NewContributorsSectionView />
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
