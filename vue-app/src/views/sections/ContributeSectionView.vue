<script setup lang="ts">
defineProps<{
  contributeLink: string
  slackLink: string
}>()
</script>

<template>
  <section class="wof-section wof-contribute-section">
    <div class="wof-contribute-section__content">
      <h2 class="puik-h2">✨ How to contribute?</h2>
      <p class="puik-body-default">
        Join the open-source movement by contributing to PrestaShop on GitHub—whether it’s code,
        documentation, or ideas. Every contribution counts!
      </p>
    </div>
    <div class="wof-contribute-section__links">
      <puik-button
        variant="primary"
        :href="contributeLink"
      >
        Contribute
      </puik-button>
      <puik-button variant="secondary" :href="slackLink">
        Join Slack
      </puik-button>
    </div>
  </section>
</template>

<style>
.wof-contribute-section {
  justify-content: center;
  align-items: center;
  background-color: #bde9c9;
}
.wof-contribute-section__content {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.wof-contribute-section__links {
  display: flex;
  gap: 1rem;
}
.wof-contribute-section__links a.puik-button {
  text-decoration: none !important;
}
.wof-contribute-section__links a.puik-button--primary:hover {
  color: white !important;
}
</style>
