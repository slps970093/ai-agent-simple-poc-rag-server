<template>
  <div>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">選單管理</div>
      <q-btn
        v-if="isRoot"
        color="primary"
        label="新增選單"
        icon="fas fa-plus"
        @click="$inertia.visit(route('admin.menus.create'))"
      />
    </div>

    <q-card>
      <q-card-section>
        <draggable
          v-model="topMenus"
          item-key="id"
          handle=".drag-handle"
          @end="onDragEnd(null)"
        >
          <template #item="{ element }">
            <div class="q-mb-sm">
              <q-expansion-item
                v-if="element.children && element.children.length > 0"
                :icon="element.icon"
                :label="getDisplayName(element.name)"
                default-opened
                expand-separator
              >
                <template #header>
                  <q-item-section avatar>
                    <q-icon :name="element.icon" />
                  </q-item-section>
                  <q-item-section>
                    {{ getDisplayName(element.name) }}
                  </q-item-section>
                  <q-item-section side>
                    <div class="row items-center q-gutter-sm">
                      <q-badge :color="element.is_active ? 'positive' : 'negative'">
                        {{ element.is_active ? '啟用' : '停用' }}
                      </q-badge>
                      <q-btn
                        flat
                        round
                        dense
                        icon="fas fa-pen"
                        color="primary"
                        size="sm"
                        @click.stop="$inertia.visit(route('admin.menus.edit', element.id))"
                      />
                      <q-btn
                        v-if="can('admin.menus.delete')"
                        flat
                        round
                        dense
                        icon="fas fa-trash"
                        color="negative"
                        size="sm"
                        @click.stop="confirmDelete(element)"
                      />
                      <q-icon name="fas fa-grip-vertical" class="drag-handle cursor-move" />
                    </div>
                  </q-item-section>
                </template>

                <draggable
                  v-model="element.children"
                  item-key="id"
                  handle=".drag-handle"
                  @end="onDragEnd(element.id)"
                  class="q-pl-md"
                >
                  <template #item="{ element: child }">
                    <q-item dense class="q-my-xs">
                      <q-item-section avatar>
                        <q-icon :name="child.icon || 'fas fa-circle'" size="sm" />
                      </q-item-section>
                      <q-item-section>
                        {{ getDisplayName(child.name) }}
                      </q-item-section>
                      <q-item-section side>
                        <div class="row items-center q-gutter-sm">
                          <q-badge :color="child.is_active ? 'positive' : 'negative'">
                            {{ child.is_active ? '啟用' : '停用' }}
                          </q-badge>
                          <q-btn
                            flat
                            round
                            dense
                            icon="fas fa-pen"
                            color="primary"
                            size="sm"
                            @click="$inertia.visit(route('admin.menus.edit', child.id))"
                          />
                          <q-btn
        v-if="can('admin.menus.create')"
                            flat
                            round
                            dense
                            icon="fas fa-trash"
                            color="negative"
                            size="sm"
                            @click="confirmDelete(child)"
                          />
                          <q-icon name="fas fa-grip-vertical" class="drag-handle cursor-move" />
                        </div>
                      </q-item-section>
                    </q-item>
                  </template>
                </draggable>
              </q-expansion-item>

              <q-item v-else dense class="q-my-xs">
                <q-item-section avatar>
                  <q-icon :name="element.icon || 'fas fa-circle'" />
                </q-item-section>
                <q-item-section>
                  {{ getDisplayName(element.name) }}
                </q-item-section>
                <q-item-section side>
                  <div class="row items-center q-gutter-sm">
                    <q-badge :color="element.is_active ? 'positive' : 'negative'">
                      {{ element.is_active ? '啟用' : '停用' }}
                    </q-badge>
                    <q-btn
                      flat
                      round
                      dense
                      icon="edit"
                      color="primary"
                      size="sm"
                      @click="$inertia.visit(route('admin.menus.edit', element.id))"
                    />
                    <q-btn
                      v-if="isRoot"
                      flat
                      round
                      dense
                      icon="delete"
                      color="negative"
                      size="sm"
                      @click="confirmDelete(element)"
                    />
                    <q-icon name="fas fa-grip-vertical" class="drag-handle cursor-move" />
                  </div>
                </q-item-section>
              </q-item>
            </div>
          </template>
        </draggable>
      </q-card-section>
    </q-card>

    <q-dialog v-model="deleteDialog.show">
      <q-card>
        <q-card-section>
          <div class="text-h6">確認刪除</div>
        </q-card-section>
        <q-card-section>
          確定要刪除選單「{{ getDisplayName(deleteDialog.menu?.name) }}」嗎？
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="取消" v-close-popup />
          <q-btn color="negative" label="刪除" @click="deleteMenu" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import AdminLayout from '../../Layouts/AdminLayout.vue'
import { useCan } from '../../Composables/useCan'

defineOptions({
  layout: AdminLayout,
})

const { can } = useCan()

const props = defineProps<{
  menus: any[]
}>()

const topMenus = ref([...props.menus])

const getDisplayName = (name: any) => {
  if (typeof name === 'object') {
    return name.zh_TW || name.en || Object.values(name)[0]
  }
  return name
}

const onDragEnd = (parentId: number | null) => {
  const items = parentId === null
    ? topMenus.value.map((item, index) => ({ id: item.id, sort_order: index }))
    : topMenus.value
        .find(m => m.id === parentId)
        ?.children.map((item: any, index: number) => ({ id: item.id, sort_order: index })) || []

  router.post(route('admin.menus.update-order'), { items }, {
    preserveScroll: true,
  })
}

const deleteDialog = ref({
  show: false,
  menu: null as any,
})

const confirmDelete = (menu: any) => {
  deleteDialog.value.menu = menu
  deleteDialog.value.show = true
}

const deleteMenu = () => {
  router.delete(route('admin.menus.destroy', deleteDialog.value.menu.id), {
    onSuccess: () => {
      deleteDialog.value.show = false
    },
  })
}
</script>

<style scoped>
.drag-handle {
  cursor: move;
  color: #999;
}
.drag-handle:hover {
  color: #333;
}
</style>
