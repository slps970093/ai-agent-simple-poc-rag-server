<template>
  <q-layout view="hHh lpR fFf">
    <q-header class="bg-white text-dark" bordered>
      <q-toolbar class="q-px-md">
        <q-btn
          flat
          dense
          round
          icon="fas fa-bars"
          @click="toggleLeftDrawer"
        />
        <q-toolbar-title class="text-primary text-weight-bold">
          Comet Admin
        </q-toolbar-title>
        <q-space />
        <q-btn flat round dense icon="fas fa-bell" />
        <q-btn-dropdown flat no-caps>
          <template #label>
            <div class="row items-center no-wrap">
              <q-avatar size="32px" color="primary" text-color="white">
                {{ userInitial }}
              </q-avatar>
              <div class="q-ml-sm">{{ userName }}</div>
            </div>
          </template>
          <q-list>
            <q-item clickable v-close-popup @click="logout">
              <q-item-section avatar>
                <q-icon name="fas fa-right-from-bracket" />
              </q-item-section>
              <q-item-section>登出</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :width="260"
      class="bg-dark"
    >
      <div class="q-pa-md text-center">
        <div class="text-h6 text-white text-weight-bold">Comet Admin</div>
        <div class="text-caption text-grey-6">管理後台</div>
      </div>
      <q-separator dark />
      <sidebar-menu :items="menuItems" />
    </q-drawer>

    <q-page-container>
      <q-page class="q-pa-md bg-grey-3">
        <slot />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import SidebarMenu from '../Components/SidebarMenu.vue'

const page = usePage()
const leftDrawerOpen = ref(false)

const menuItems = computed(() => page.props.menu || [])
const user = computed(() => page.props.auth?.admin)

const userName = computed(() => {
  return user.value?.account || 'Admin'
})

const userInitial = computed(() => {
  return userName.value.charAt(0).toUpperCase()
})

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

const logout = () => {
  router.post('/admin/logout')
}
</script>

<style scoped>
.bg-dark {
  background-color: #343a40;
}
</style>
