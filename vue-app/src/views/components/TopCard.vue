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
  <puik-card class="wof-top-section__card">
    <div class="wof-top-section__title-container">
      <h3 class="puik-h2">{{ title }}</h3>
      <puik-button
        v-if="externalLinkContent && externalLinkHref"
        variant="secondary"
        :aria-label="externalLinkContent"
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
    <p v-if="description" class="puik-body-default">{{ description }}</p>

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
.wof-top-section__card h3,
.wof-top-section__card p {
  margin-bottom: 1rem;
}
.wof-top-section__title-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
</style>
