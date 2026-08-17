<template>
  <div>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">權限管理</div>
      <q-btn
        v-if="can('admin.permissions.create')"
        color="primary"
        label="新增權限"
        icon="fas fa-plus"
        @click="$inertia.visit(route('admin.permissions.create'))"
      />
    </div>

    <q-card>
      <q-table
        :rows="permissions"
        :columns="columns"
        row-key="id"
        flat
        bordered
      >
        <template #body-cell-key_name="props">
          <q-td :props="props">
            <q-badge color="grey-7">admin.{{ props.row.key_name }}</q-badge>
          </q-td>
        </template>

        <template #body-cell-display_name="props">
          <q-td :props="props">
            {{ getDisplayName(props.row.display_name) }}
          </q-td>
        </template>

        <template #body-cell-actions="props">
          <q-td :props="props">
            <q-btn
              flat
              round
              dense
              icon="fas fa-pen"
              color="primary"
              @click="$inertia.visit(route('admin.permissions.edit', props.row.id))"
            />
            <q-btn
              v-if="can('admin.permissions.delete')"
              flat
              round
              dense
              icon="fas fa-trash"
              color="negative"
              @click="confirmDelete(props.row)"
            />
          </q-td>
        </template>
      </q-table>
    </q-card>

    <q-dialog v-model="deleteDialog.show">
      <q-card>
        <q-card-section>
          <div class="text-h6">確認刪除</div>
        </q-card-section>
        <q-card-section>
          確定要刪除權限「admin.{{ deleteDialog.permission?.key_name }}」嗎？
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="取消" v-close-popup />
          <q-btn color="negative" label="刪除" @click="deletePermission" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'
import { useCan } from '../../Composables/useCan'

defineOptions({
  layout: AdminLayout,
})

const { can } = useCan()

const props = defineProps<{
  permissions: any[]
}>()

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'key_name', label: '識別碼', field: 'key_name', align: 'left', sortable: true },
  { name: 'display_name', label: '顯示名稱', field: 'display_name', align: 'left' },
  { name: 'actions', label: '操作', field: 'actions', align: 'center' },
]

const getDisplayName = (name: any) => {
  if (typeof name === 'object') {
    return name.zh_TW || name.en || Object.values(name)[0]
  }
  return name
}

const deleteDialog = ref({
  show: false,
  permission: null as any,
})

const confirmDelete = (permission: any) => {
  deleteDialog.value.permission = permission
  deleteDialog.value.show = true
}

const deletePermission = () => {
  router.delete(route('admin.permissions.destroy', deleteDialog.value.permission.id), {
    onSuccess: () => {
      deleteDialog.value.show = false
    },
  })
}
</script>
