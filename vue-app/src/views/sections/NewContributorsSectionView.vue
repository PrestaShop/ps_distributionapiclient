<script setup lang="ts">
import 'vue3-carousel/carousel.css'
import { Carousel, Slide, Navigation } from 'vue3-carousel'
import type { NewContributor } from '@/types'

defineProps<{
  newContributors: NewContributor[]
}>()

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
</script>

<template>
  <section class="wof-section wof-new-contributors-section">
    <div>
      <h2 class="wof-new-contributors-section__title puik-h2">👋 Say hello to our new contributors</h2>
      <p class="wof-new-contributors-section__description puik-body-default">
        Fresh commits, fresh faces. Meet the contributors who just joined!
      </p>
    </div>
    <Carousel v-bind="carousel_config">
      <Slide v-for="(newContributor, index) in newContributors" :key="index">
        <puik-card class="wof-new-contributors-section__card">
          <img
            class="wof-new-contributors-section__img"
            :src="newContributor.avatar_url"
            :alt="`${newContributor.name ?? newContributor.login} avatar`"
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
</template>

<style>
:root {
  --wof-new-contributors-section-bg: #a4dbe8;
  --wof-carousel-nav-bg: #fff;
  --wof-carousel-nav-border: #1d1d1b;
  --wof-carousel-nav-disabled-bg: #f7f7f7;
  --wof-carousel-nav-disabled-border: #ddd;
  --wof-carousel-nav-hover: #000;
}

.wof-new-contributors-section {
  background-color: var(--wof-new-contributors-section-bg);
}
.wof-new-contributors-section__card {
  flex-grow: 1;
  gap: 0;
}
.wof-new-contributors-section__card * {
  margin-bottom: 0;
}
.wof-new-contributors-section__title {
  margin-bottom: 1rem;
}
.wof-new-contributors-section__description {
  margin-bottom: 0;
  padding-right: 96px;
}
.wof-new-contributors-section__img {
  width: 100%;
  object-fit: cover;
  object-position: center;
}

.carousel {
  --vc-nav-border-radius: 50%;
  --vc-nav-width: 36px;
  --vc-nav-height: 36px;
}
.wof-carousel__nav-container {
  margin: 1rem 0 1.5rem 1rem;
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
  background: var(--vc-nav-background, var(--wof-carousel-nav-bg));
  background-color: var(--wof-carousel-nav-bg);
  border: 1px solid var(--wof-carousel-nav-border);
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
  background-color: var(--wof-carousel-nav-disabled-bg);
  border-color: var(--wof-carousel-nav-disabled-border);
  opacity: 1;
}

.wof-carousel__nav-container .carousel__next--disabled .puik-icon,
.wof-carousel__nav-container .carousel__prev--disabled .puik-icon {
  opacity: 0.3;
}

.wof-carousel__nav-container .carousel__next:hover,
.wof-carousel__nav-container .carousel__prev:hover {
  color: var(--wof-carousel-nav-hover);
}
</style>
