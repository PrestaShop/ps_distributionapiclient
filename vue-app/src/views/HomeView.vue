<script setup lang="ts">
import 'vue3-carousel/carousel.css'

import { ref, onMounted } from 'vue'
import { Carousel, Slide, Navigation } from 'vue3-carousel'
import HeaderSectionView from './sections/HeaderSectionView.vue';
import TopSectionView from './sections/TopSectionView.vue';

const carousel_config = {
  itemsToShow: 1,
  gap: 16,
  snapAlign: 'center' as const,
  breakpointMode: 'carousel' as const,
  breakpoints: {
    0: {
      itemsToShow: 1,
      snapAlign: 'center' as const,
    },
    476: {
      itemsToShow: 2,
      snapAlign: 'start' as const,
    },
    992: {
      itemsToShow: 3,
      snapAlign: 'start' as const,
    },
    1024: {
      itemsToShow: 4,
      snapAlign: 'start' as const,
    },
    1200: {
      itemsToShow: 5,
      snapAlign: 'start' as const,
    },
    1600: {
      itemsToShow: 6,
      snapAlign: 'start' as const,
    },
  },
}

const newContributors = ref()

onMounted(async () => {
  try {
    const response = await fetch('https://contributors.prestashop-project.org/newcontributors.json')
    if (!response.ok) throw new Error('Error loading new contributors')
    const data = await response.json()
    newContributors.value = data
  } catch (error) {
    console.error('Error :', error)
  }
})
</script>

<template>
  <div class="wof-container">
    <HeaderSectionView />
    <main>
      <TopSectionView />
      <section class="wof-section wof-new-contributors__section">
        <div>
          <h2 class="puik-h2">👋 Say hello to our new contributors</h2>
          <p class="puik-body-default">
            Fresh commits, fresh faces. Meet the contributors who just joined!
          </p>
        </div>
        <Carousel v-bind="carousel_config">
          <Slide v-for="(newContributor, index) in newContributors" :key="index">
            <puik-card class="wof-new-contributors__card">
              <img
                class="wof-new-contributors__img"
                :src="newContributor.avatar_url"
                :alt="newContributor"
              />
              <h3 class="puik-h3">{{ newContributor.name ?? newContributor.login}}</h3>
              <p class="puik-body-default">{{ newContributor.login }}</p>
              <p class="puik-body-small">{{ newContributor.contributions }} contribution{{ newContributor.contributions > 1 ? "s" : "" }}</p>
            </puik-card>
          </Slide>
          <template #addons>
            <div class="wof-carousel__nav-container">
              <Navigation>
                <template #prev>
                  <puik-icon icon="keyboard_arrow_left" />
                </template>
                <template #next>
                  <puik-icon icon="keyboard_arrow_right" />
                </template>
              </Navigation>
            </div>
          </template>
        </Carousel>
      </section>
      <section class="wof-section wof-wall__section">
        <div>
          <h2 class="puik-h2">🏆 PrestaShop Project’s Wall of fame</h2>
          <p class="puik-body-default">
            The PrestaShop Wall of Fame: built by the best, committed to the core.
          </p>
        </div>
      </section>
      <section class="wof-section wof-contribute__section">
        <div class="wof-contribute__content">
          <h2 class="puik-h2">✨ How to contribute?</h2>
          <p class="puik-body-default">
            Join the open-source movement by contributing to PrestaShop on GitHub—whether it’s code,
            documentation, or ideas. Every contribution counts!
          </p>
        </div>
        <div class="wof-contribute_links">
          <puik-button
            variant="primary"
            href="https://github.com/PrestaShop/PrestaShop/blob/develop/CONTRIBUTING.md"
          >
            Contribute
          </puik-button>
          <puik-button variant="secondary" href="https://www.prestashop-project.org/slack/">
            Join Slack
          </puik-button>
        </div>
      </section>
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
.wof-new-contributors__section {
  background-color: #a4dbe8;
}
.wof-contribute__section {
  justify-content: center;
  align-items: center;
  background-color: #bde9c9;
}
.wof-contribute__content {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.wof-new-contributors__card {
  flex-grow: 1;
  gap: 0.25rem;
}
.wof-new-contributors__card * {
  margin-bottom: 0;
}
.wof-new-contributors__img {
  width: 100%;
  object-fit: cover;
  object-position: center;
}
.wof-contribute_links {
  display: flex;
  gap: 1rem;
}
.wof-contribute_links a.puik-button {
  text-decoration: none !important;
}
.wof-contribute_links a.puik-button--primary:hover {
  color: white !important;
}

.carousel {
  --vc-nav-border-radius: 50%;
  --vc-nav-width: 36px;
  --vc-nav-height: 36px;
}
.wof-carousel__nav-container {
  margin: 1rem 0 2rem 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  right: 0;
  bottom: 100%;
  gap: 0.5rem;
}
.wof-carousel__nav-container .carousel__next,
.wof-carousel__nav-container .carousel__prev {
  background: var(--vc-nav-background);
  background-color: white;
  border: 1px solid #1d1d1b;
  border-radius: var(--vc-nav-border-radius);
  color: var(--vc-nav-color);
  font-size: var(--vc-nav-height);
  height: var(--vc-nav-height);
  position: relative;
  transform: translateY(0);
  width: var(--vc-nav-width);
}

.wof-carousel__nav-container .carousel__next--disabled,
.wof-carousel__nav-container .carousel__prev--disabled {
  background-color: #f7f7f7;
  border-color: #ddd;
  opacity: 1;
}

.wof-carousel__nav-container .carousel__next--disabled .puik-icon,
.wof-carousel__nav-container .carousel__prev--disabled .puik-icon {
  opacity: 0.3;
}

.wof-carousel__nav-container .carousel__next:hover,
.wof-carousel__nav-container .carousel__prev:hover {
  color: black;
}
.puik-tag p {
  margin-bottom: 0;
}
</style>
