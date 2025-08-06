<script setup lang="ts">
import type { Contributor } from '@/types'

const props = defineProps<{
  contributor: Contributor
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const close = () => emit('close')
</script>

<template>
  <puik-modal
    class="wof-top-modal"
    size="large"
    variant="feedback"
    :is-open="isOpen"
    @close="close"
  >
    <puik-button class="wof-top-modal__close-btn" variant="text" size="sm" @click="close">
      <puik-icon icon="close" font-size="1.25rem"/>
    </puik-button>
    <div class="wof-top-modal__container">
      <div class="wof-top-modal__side-content">
        <div class="wof-top-modal__avatar">
          <img :src="contributor.avatar_url" alt="contributor avatar" />
        </div>
        <div class="wof-top-modal__title">
          <h3 class="puik-h3">{{ contributor.name }}</h3>
          <puik-tag v-if="contributor.company" :content="contributor.company" variant="blue" />
        </div>
        <div v-if="contributor.location" class="wof-top-modal__side-content__item">
          <puik-icon icon="location_on" :fill="0" />
          <div class="wof-top-modal__side-content__item-infos">
            <span class="wof-top-modal__side-content__item-title puik-body-default">Location</span>
            <span class="wof-top-modal__side-content__item-value puik-body-default">
              {{ contributor.location }}
            </span>
          </div>
        </div>
        <div v-if="contributor.company" class="wof-top-modal__side-content__item">
          <puik-icon icon="work" :fill="0" />
          <div class="wof-top-modal__side-content__item-infos">
            <span class="wof-top-modal__side-content__item-title puik-body-default">
              Current role(s)
            </span>
            <span class="wof-top-modal__side-content__item-value puik-body-default">
              {{ contributor.company}}
            </span>
          </div>
        </div>
        <div v-if="contributor.html_url" class="wof-top-modal__side-content__item">
          <puik-icon icon="location_on" :fill="0" />
          <div class="wof-top-modal__side-content__item-infos">
            <span class="wof-top-modal__side-content__item-title puik-body-default">GitHub</span>
            <puik-link
              :href="contributor.html_url"
              target="_blank"
              aria-label="contributor github"
              class="wof-top-modal__side-content__item-value puik-body-default"
            >
              {{ contributor.html_url }}
            </puik-link>
          </div>
        </div>
        <div v-if="contributor.blog" class="wof-top-modal__side-content__item">
          <puik-icon icon="desktop_mac" :fill="0" />
          <div class="wof-top-modal__side-content__item-infos">
            <span class="wof-top-modal__side-content__item-title puik-body-default">Website</span>
            <puik-link
              :href="contributor.blog"
              target="_blank"
              aria-label="contributor blog"
              class="wof-top-modal__side-content__item-value puik-body-default"
            >
              {{ contributor.blog }}
            </puik-link>
          </div>
        </div>
      </div>

      <div class="wof-top-modal__main-content">
        <p class="puik-body-default-medium">{{ contributor.contributions }} contributions</p>
        <div class="wof-top-modal__categories">
          <puik-card
            class="wof-top-modal__categories__card"
            v-for="(data, category) in contributor.categories"
            :key="category"
          >
            <p class="puik-h2">{{ data.total }}</p>
            <p class="puik-body-default">{{ category }}</p>
          </puik-card>
        </div>
      </div>
    </div>
  </puik-modal>
</template>

<style>
:root {
  --wof-color-bg-modal: #ffffff;
  --wof-color-bg-modal-side-panel: #dddddd;
  --wof-color-side-panel-item-value: #5e5e5e;
  --wof-padding-top-modal: 8.5rem;
}

.wof-top-modal__close-btn {
  position: absolute;
  right: 1rem;
  top: 1rem;
}
.wof-top-modal .puik-modal__dialogPanelContainer__dialogPanel {
  background-color: var(--wof-color-bg-modal);
  padding: 0;
}
.wof-top-modal .puik-modal__dialogPanelContainer {
  padding-top: var(--wof-padding-top-modal);
}
.wof-top-modal__container {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  background-color: var(--wof-color-bg-modal-side-panel);
  overflow: auto;
  @media screen and (min-width: 768px) {
    flex-direction: row;
  }
}
.wof-top-modal__title h3 {
  margin-bottom: 0;
}
.wof-top-modal__side-content {
  padding: 20px;
  min-width: min-content;
  min-height: fit-content;
  display: flex;
  flex-direction: column;
  align-items: self-start;
  gap: 1rem;
  background-color: var(--wof-color-bg-modal);
  overflow-y: auto;
    @media screen and (min-width: 768px) {
      padding: 40px;
  }
}
.wof-top-modal__avatar {
  border-radius: 50%;
  min-height: 128px;
  overflow: hidden;
}
.wof-top-modal__avatar img {
  width: 128px;
  height: 128px;
  object-fit: cover;
  object-position: center;
  border-radius: 50%;
}
.wof-top-modal__side-content__item {
  display: flex;
  align-items: start;
  gap: 0.5rem;
}
.wof-top-modal__side-content__item-infos {
  display: flex;
  flex-direction: column;
}
.wof-top-modal__side-content__item-title {
  line-height: 1;
}
.wof-top-modal__side-content__item-value {
  color: var(--wof-color-side-panel-item-value);
}
.wof-top-modal__main-content {
  padding: 20px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  min-height: fit-content;
  gap: 1rem;
  overflow: auto;
  @media screen and (min-width: 768px) {
    padding: 40px;
  }
}
.wof-top-modal__categories {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}
.wof-top-modal__categories__card {
  padding: 1rem;
  max-height: fit-content;
  display: flex;
  flex-direction: column;
  gap: 0;
}
.wof-top-modal__categories__card p {
  margin: 0;
}
</style>
