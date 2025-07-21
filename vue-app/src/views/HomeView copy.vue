<script setup lang="ts">
import 'vue3-carousel/carousel.css'

import { ref, onMounted } from 'vue'
import { type PuikTableHeader } from '@prestashopcorp/puik-components'
import { Carousel, Slide, Navigation } from 'vue3-carousel'

const isModalOpen = ref(false);
const openModal = (contributor: any) => {
  modalContributorItem.value = contributor
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};

const expandable = ref(false)
const selection = ref([])
const selectable = ref(false)
const searchBar = ref(false)
const searchFromServer = ref(false)
const sortFromServer = ref(false)
const fullWidth = ref(true)
const stickyFirstCol = ref(false)
const stickyLastCol = ref(false)

const headers: PuikTableHeader[] = [
  {
    text: 'Rank',
    value: 'rank',
    size: 'sm',
    align: 'left',
    searchable: false,
  },
  {
    text: 'Avatar',
    value: 'avatar',
    size: 'sm',
    align: 'center',
    searchable: false,
  },
  {
    text: 'Name',
    value: 'name',
    size: 'md',
    searchable: true,
  },
  {
    text: 'Contributions',
    value: 'contributions',
    size: 'sm',
    align: 'center',
    searchable: false,
  },
  {
    value: 'actions',
    size: 'sm',
    align: 'center',
    preventExpand: true,
    searchSubmit: true,
  },
]

const carousel_config = {
  itemsToShow: 1,
  gap: 16,
  snapAlign: 'center' as const,
  breakpointMode: 'carousel' as const,
  breakpoints: {
    664: {
      itemsToShow: 2,
      snapAlign: 'start' as const,
    },
    1024: {
      itemsToShow: 3,
      snapAlign: 'start' as const,
    },
    1200: {
      itemsToShow: 4,
      snapAlign: 'start' as const,
    },
    1600: {
      itemsToShow: 5,
      snapAlign: 'start' as const,
    },
  },
}

const contributorsData = ref()
const newContributors = ref([])
const topCompanies = ref<string | null>()
const topContributors = ref()
const modalContributorItem = ref();
const totalContribs = ref()
const prestaContribs = ref()

onMounted(async () => {
  try {
    const response = await fetch('https://contributors.prestashop-project.org/newcontributors.json')
    if (!response.ok) throw new Error('Error loading new contributors')
    const data = await response.json()
    newContributors.value = data
  } catch (error) {
    console.error('Error :', error)
  }

  try {
    const response = await fetch('https://contributors.prestashop-project.org/topcompanies.json')
    if (!response.ok) throw new Error('Error loading top companies')
    const data = await response.json()
    topCompanies.value = data

    const total = data.reduce((acc: number, company: any) => acc + company.contributions, 0)
    totalContribs.value = total

    const presta = data.find((c: any) => c.name.toLowerCase() === 'prestashop')
    prestaContribs.value = presta.contributions

  } catch (error) {
    console.error('Error loading top companies:', error)
  }

  try {
    const response = await fetch('https://contributors.prestashop-project.org/contributors.json')
    if (!response.ok) throw new Error('Error loading contributors data')
    const data = await response.json()
    contributorsData.value = Object.values(data)
    topContributors.value = contributorsData.value.slice(0, 5)
  } catch (error) {
    console.error('Error :', error)
  }
})
</script>

<template>
  <div class="wof-container">
    <header class="wof-header">
      <h1 class="puik-brand-jumbotron wof-title">MEET our community Heroes</h1>
      <p class="puik-body-default wof-description">
        From day one, PrestaShop has thrived as an open-source platform powered by a talented
        community of developers, merchants, and contributors. We all work together to improve and
        support the scalability of the PrestaShop e-commerce platform. By remaining the main
        contributors to its development, PrestaShop ensures long-term sustainability for everyone in
        the ecosystem. The project grows with each contribution, and with each contribution our
        contributors’ expertise grows. Take a look at our community.
      </p>
      <div class="contrib-kpis__container">
        <div class="contrib-kpis__item">
          <span class="contrib-kpis__value puik-brand-h1">{{ totalContribs }}</span>
          <span class="contrib-kpis__label puik-body-default">Total Contributions</span>
        </div>
        <div class="contrib-kpis__item">
          <span class="contrib-kpis__value puik-brand-h1">
            {{ Math.round((prestaContribs / totalContribs) * 100) }}%
          </span>
          <span class="contrib-kpis__label puik-body-default">Contribs by PrestaShop</span>
        </div>
        <div class="contrib-kpis__item">
          <span class="contrib-kpis__value puik-brand-h1">
            {{ Math.round(100 - (prestaContribs / totalContribs) * 100) }}%
          </span>
          <span class="contrib-kpis__label puik-body-default">Contribs by Community</span>
        </div>
      </div>
    </header>
    <main>
      <section class="wof-section wof-top-contributors__section">
        <h2 class="puik-h1">PrestaShop Project’s top contributors</h2>
        <div class="wof-top-contributors__cards">
          <puik-card class="wof-top-contributors__card">
            <h3 class="puik-h2">🚀 Top companies</h3>
            <p class="puik-body-default">
              Meet the top companies who are helping us strengthen PrestaShop.
            </p>
            <puik-table
              v-if="topContributors"
              v-model:selection="selection"
              :headers="headers"
              :items="topContributors"
              :expandable="expandable"
              :selectable="selectable"
              :searchBar="searchBar"
              :searchFromServer="searchFromServer"
              :sortFromServer="sortFromServer"
              :fullWidth="fullWidth"
              :stickyFirstCol="stickyFirstCol"
              :stickyLastCol="stickyLastCol"
            >
              <template #item-rank="{ item, index }">
                <div
                  :class="[
                    'wof-top-contributors__rank',
                    { 'wof-top-contributors__rank--first': index === 0 },
                    { 'wof-top-contributors__rank--second': index === 1 },
                    { 'wof-top-contributors__rank--third': index === 2 }
                    ]">
                  <span class="puik-body-default-bold">{{index + 1}}</span>
                </div>
              </template>
              <template #item-avatar="{ item }">
                <puik-avatar size="large" type="photo" :src="item.avatar_url" />
              </template>
              <template #item-name="{ item }">
                <div class="wof-top-contributors__name">
                  <span class="puik-body-default">{{ item.name }}</span>
                  <puik-tag :content="item.company" variant="blue" />
                </div>
              </template>
              <template #item-actions="{ item }">
                <puik-button @click="openModal(item)" variant="text" right-icon="visibility" aria-label="view profile" />
              </template>
            </puik-table>
          </puik-card>
          <puik-card class="wof-top-contributors__card">
            <h3 class="puik-h2">🔥 Top contributors</h3>
            <p class="puik-body-default">
              These experts spent hours improving PrestaShop's quality.
            </p>
            <puik-table
              v-if="topContributors"
              v-model:selection="selection"
              :headers="headers"
              :items="topContributors"
              :expandable="expandable"
              :selectable="selectable"
              :searchBar="searchBar"
              :searchFromServer="searchFromServer"
              :sortFromServer="sortFromServer"
              :fullWidth="fullWidth"
              :stickyFirstCol="stickyFirstCol"
              :stickyLastCol="stickyLastCol"
            >
              <template #item-rank="{ item, index }">
                <div
                  :class="[
                    'wof-top-contributors__rank',
                    { 'wof-top-contributors__rank--first': index === 0 },
                    { 'wof-top-contributors__rank--second': index === 1 },
                    { 'wof-top-contributors__rank--third': index === 2 }
                    ]">
                  <span class="puik-body-default-bold">{{index + 1}}</span>
                </div>
              </template>
              <template #item-avatar="{ item }">
                <puik-avatar size="large" type="photo" :src="item.avatar_url" />
              </template>
              <template #item-name="{ item }">
                <div class="wof-top-contributors__name">
                  <span class="puik-body-default">{{ item.name }}</span>
                  <puik-tag :content="item.company" variant="blue" />
                </div>
              </template>
              <template #item-actions="{ item }">
                <puik-button @click="openModal(item)" variant="text" right-icon="visibility" aria-label="view profile" />
              </template>
            </puik-table>
          </puik-card>
        </div>
      </section>
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
              <div>
                <img
                  class="wof-new-contributors__img"
                  :src="`https://picsum.photos/seed/1/800/600`"
                  :alt="newContributor"
                />
                <h3 class="puik-h3">{{ newContributor }}</h3>
                <p class="puik-body-default">{{ newContributor }} contributions</p>
              </div>
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
      <puik-modal
          id="wof-contributor-modal"
          size='large'
          variant='feedback'
          :is-open="isModalOpen"
          @close="closeModal"
        >
        <div class="wof-contributor-modal">
          <div class="wof-contributor-modal__side-content">
            <div class="wof-contributor-modal__avatar">
              <img :src="modalContributorItem.avatar_url" alt="contributor avatar" />
            </div>
            <div class="wof-contributor-modal__title">
              <h3 class="puik-h3">{{ modalContributorItem.name }}</h3>
              <puik-tag
                v-if="modalContributorItem.company"
                :content="modalContributorItem.company"
                variant="blue"
              />
            </div>
            <div v-if="modalContributorItem.location" class="wof-contributor-modal__side-content__item">
              <puik-icon icon="location_on" :fill="0" />
              <div class="wof-contributor-modal__side-content__item--infos">
                <span class="wof-contributor-modal__side-content__item--title puik-body-default">Location</span>
                <span class="wof-contributor-modal__side-content__item--value puik-body-default">{{ modalContributorItem.location }}</span>
              </div>
            </div>
            <div v-if="modalContributorItem.company" class="wof-contributor-modal__side-content__item">
              <puik-icon icon="work" :fill="0" />
              <div class="wof-contributor-modal__side-content__item--infos">
                <span class="wof-contributor-modal__side-content__item--title puik-body-default">Current role(s)</span>
                <span class="wof-contributor-modal__side-content__item--value puik-body-default">{{ modalContributorItem.company }}</span>
              </div>
            </div>
            <div v-if="modalContributorItem.html_url" class="wof-contributor-modal__side-content__item">
              <puik-icon icon="location_on" :fill="0" />
              <div class="wof-contributor-modal__side-content__item--infos">
                <span class="wof-contributor-modal__side-content__item--title puik-body-default">Gitub</span>
                <puik-link
                  :href="modalContributorItem.html_url"
                  target="_blank"
                  aria-label="contributor github"
                  class="wof-contributor-modal__side-content__item--value puik-body-default"
                  >
                  {{ modalContributorItem.html_url }}
                </puik-link>
              </div>
            </div>
            <div v-if="modalContributorItem.blog" class="wof-contributor-modal__side-content__item">
              <puik-icon icon="desktop_mac" :fill="0" />
              <div class="wof-contributor-modal__side-content__item--infos">
                <span class="wof-contributor-modal__side-content__item--title puik-body-default">Website</span>
                <puik-link
                  :href="modalContributorItem.blog"
                  target="_blank"
                  aria-label="contributor blog"
                  class="wof-contributor-modal__side-content__item--value puik-body-default"
                  >
                  {{ modalContributorItem.blog }}
                </puik-link>
              </div>
            </div>
          </div>
          <div class="wof-contributor-modal__main-content">
            <p class="puik-body-default-medium">{{ modalContributorItem.contributions }} contributions</p>
            <div class="wof-contributor-modal__categories">
              <puik-card class="wof-contributor-modal__categories__card" v-for="(data, category) in modalContributorItem.categories" :key="category">
                <p class="puik-h2">{{ data.total }}</p>
                <p class="puik-body-default">{{ category }}</p>
              </puik-card>
            </div>
          </div>
        </div>
      </puik-modal>
    </main>
  </div>
</template>

<style>
.wof-title {
  text-transform: uppercase;
  text-align: center;
}
.wof-description {
  text-align: center;
}
.wof-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  background-color: #1d1d1b;
  padding: 1rem;
}
@media (min-width: 768px) {
  .wof-header {
    padding: 4rem;
  }
}
.wof-header * {
  color: white;
}
.contrib-kpis__container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
}
.contrib-kpis__item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 190px;
}
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
.wof-top-contributors__cards {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
}
.wof-top-contributors__card {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  max-width: 100%;
  gap: 0 !important;
}
.wof-top-contributors__rank {
  width: 1.5rem;
  height: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}
.wof-top-contributors__rank span{
  line-height: 0;
}
.wof-top-contributors__rank--first {
  background-color: #FFD999;
}
.wof-top-contributors__rank--second {
  background-color: #EEEEEE;
}
.wof-top-contributors__rank--third {
  background-color: #E7BD94;
}
.wof-contributor-modal {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  background-color: #DDDDDD;
  overflow: auto;
  @media screen and (min-width: 768px) {
    flex-direction: row;
  }
}
.wof-contributor-modal__title h3 {
  margin-bottom: 0;
}
#wof-contributor-modal .puik-modal__dialogPanelContainer__dialogPanel {
  background-color: #DDDDDD;
  padding: 0;
}
.wof-contributor-modal__side-content {
  padding: 40px;
  min-width: min-content;
  min-height: fit-content;
  display: flex;
  flex-direction: column;
  align-items: self-start;
  gap: 1rem;
  background-color: white;
  overflow-y: auto;
}
.wof-contributor-modal__avatar {
  border-radius: 50%;
  min-height: 128px;
  overflow: hidden;
}
.wof-contributor-modal__avatar img {
  width: 128px;
  height: 128px;
  object-fit: cover;
  object-position: center;
  border-radius: 50%;
}
.wof-contributor-modal__side-content__item {
  display: flex;
  align-items: start;
  gap: 0.5rem;
}
.wof-contributor-modal__side-content__item--infos {
  display: flex;
  flex-direction: column;
}
.wof-contributor-modal__side-content__item--title {
  line-height: 1;
}
.wof-contributor-modal__side-content__item--value {
  color: #5E5E5E;
}
.wof-contributor-modal__main-content {
  padding: 40px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  overflow: auto;
}
.wof-contributor-modal__categories {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}
.wof-contributor-modal__categories__card {
  padding: 1rem;
  max-height: fit-content;
  display: flex;
  flex-direction: column;
  gap: 0;
}
.wof-contributor-modal__categories__card p{
  margin: 0;
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
}
.wof-new-contributors__img {
  width: 100%;
  height: 166px;
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
