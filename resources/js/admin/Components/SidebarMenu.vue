<template>
  <q-list dark>
    <template v-for="item in items" :key="item.id">
      <q-expansion-item
        v-if="item.children && item.children.length > 0"
        :icon="item.icon"
        :label="item.name"
        :default-opened="isChildActive(item.children)"
        expand-icon-class="text-white"
        class="text-grey-3"
        active-class="bg-primary text-white"
      >
        <sidebar-menu :items="item.children" :nested="true" />
      </q-expansion-item>

      <q-item
        v-else
        clickable
        :active="isActive(item)"
        active-class="bg-primary text-white"
        class="text-grey-3"
        :inset-level="nested ? 0.5 : 0"
        @click="navigate(item)"
      >
        <q-item-section avatar>
          <q-icon :name="item.icon || 'fas fa-circle'" />
        </q-item-section>
        <q-item-section>{{ item.name }}</q-item-section>
      </q-item>
    </template>
  </q-list>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'

interface MenuItem {
  id: number
  name: string
  icon?: string
  url?: string
  route_name?: string
  children?: MenuItem[]
}

defineProps<{
  items: MenuItem[]
  nested?: boolean
}>()

const page = usePage()

const navigate = (item: MenuItem) => {
  if (item.url) {
    router.visit(item.url)
  }
}

const isActive = (item: MenuItem) => {
  if (!item.url) return false
  return page.url === item.url || page.url.startsWith(item.url + '/')
}

const isChildActive = (children: MenuItem[]) => {
  return children.some(child => {
    if (child.children) {
      return isChildActive(child.children)
    }
    return isActive(child)
  })
}
</script>
