<script setup lang="ts">
import { type PuikTableHeader } from '@prestashopcorp/puik-components'

defineProps<{
  title: string
  description?: string
  externalLinkHref?: string
  externalLinkContent?: string
  headers: PuikTableHeader[]
  items: any[]
  stickyLastCol?: boolean
  fullWidth?: boolean
}>()

const emit = defineEmits<{
  (e: 'view', item: any): void
  (e: 'action', payload: any): void
}>()
</script>

<template>
  <puik-card class="wof-top-card">
    <div class="wof-top-card__title-container">
      <h3 class="wof-top-card__title puik-h2">{{ title }}</h3>
      <puik-button
        v-if="externalLinkContent && externalLinkHref"
        variant="secondary"
        :aria-label="externalLinkContent"
        class="wof-top-card__external-link"
      >
        <puik-link
          :href="externalLinkHref"
          target="_blank"
          :aria-label="externalLinkContent"
        >
          {{ externalLinkContent }}
        </puik-link>
      </puik-button>
    </div>
    <p v-if="description" class="wof-top-card__description puik-body-default">{{ description }}</p>

    <puik-table
      v-if="items?.length"
      :headers="headers"
      :items="items"
      :stickyLastCol="stickyLastCol"
      :fullWidth="fullWidth"
    >
      <template
        v-for="header in headers"
        :key="header.value"
        v-slot:[`item-${header.value}`]="slotProps"
      >
        <slot
          :name="`item-${header.value}`"
          v-bind="slotProps"
        >
          <span class="puik-body-default">{{ slotProps.item[header.value] }}</span>
        </slot>
      </template>
    </puik-table>
  </puik-card>
</template>

<style>
:root {
  --wof-top-card-title-size-sm: 1.25rem;
}
.wof-top-card {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  max-width: 100%;
  gap: 0 !important;
}
.wof-top-card__title-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.wof-top-card__title,
.wof-top-card__description,
.wof-top-card__external-link {
  margin-bottom: 1rem;
}
@media (max-width: 768px) {
  .wof-top-card__title {
    font-size: var(--wof-top-card-title-size-sm);
  }

}
</style>
