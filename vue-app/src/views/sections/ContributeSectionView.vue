<script setup lang="ts">
defineProps<{
  contributeLink: string
  slackLink: string
}>()
</script>

<template>
  <section class="wof-section wof-contribute-section">
    <div class="wof-contribute-section__content">
      <h2 class="wof-contribute-section__tite puik-h2">✨ How to contribute?</h2>
      <p class="wof-contribute-section__description puik-body-default">
        Join the open-source movement by contributing to PrestaShop on GitHub—whether it’s code,
        documentation, or ideas. Every contribution counts!
      </p>
    </div>
    <div class="wof-contribute-section__links">
      <a
        :href="contributeLink"
        target="_blank"
        aria-label="Contribute to PrestaShop"
        rel="noopener noreferrer"
      >
        <puik-button variant="primary">
          Contribute
        </puik-button>
      </a>
      <a
        :href="slackLink"
        target="_blank"
        aria-label="join PrestaShop Slack Open Source"
        rel="noopener noreferrer"
      >
        <puik-button variant="secondary">
          Join Slack
        </puik-button>
      </a>
    </div>
  </section>
</template>

<style>
:root {
  --wof-contribute-section-bg: #bde9c9;
  --wof-contribute-section-link-hover-color: #ffffff;
  --wof-description-max-width: 1200px;
}
.wof-contribute-section {
  justify-content: center;
  align-items: center;
  background-color: var(--wof-contribute-section-bg);
}
.wof-contribute-section__content {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.wof-contribute-section__tite {
  margin-bottom: 1rem;
}
.wof-contribute-section__description {
  margin-bottom: 0;
  max-width: var(--wof-description-max-width);
  text-align: center;
}
.wof-contribute-section__links {
  display: flex;
  gap: 1rem;
}
.wof-contribute-section__links a.puik-button {
  text-decoration: none !important;
}
.wof-contribute-section__links a.puik-button--primary:hover {
  color: var(--wof-contribute-section-link-hover-color) !important;
}
</style>
