<template>
  <div>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">角色管理</div>
      <q-btn
        v-if="can('admin.roles.create')"
        color="primary"
        label="新增角色"
        icon="fas fa-plus"
        @click="$inertia.visit(route('admin.roles.create'))"
      />
    </div>

    <q-card>
      <q-table
        :rows="roles"
        :columns="columns"
        row-key="id"
        flat
        bordered
      >
        <template #body-cell-display_name="props">
          <q-td :props="props">
            {{ getDisplayName(props.row.display_name) }}
          </q-td>
        </template>

        <template #body-cell-is_system="props">
          <q-td :props="props">
            <q-badge v-if="props.row.is_system" color="warning">系統</q-badge>
            <span v-else class="text-grey">-</span>
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
              @click="$inertia.visit(route('admin.roles.edit', props.row.id))"
            />
            <q-btn
              v-if="can('admin.roles.delete') && !props.row.is_system"
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
          確定要刪除角色「{{ getDisplayName(deleteDialog.role?.display_name) }}」嗎？
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="取消" v-close-popup />
          <q-btn color="negative" label="刪除" @click="deleteRole" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'
import { useCan } from '../../Composables/useCan'

defineOptions({
  layout: AdminLayout,
})

const { can } = useCan()

const props = defineProps<{
  roles: any[]
}>()

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'display_name', label: '顯示名稱', field: 'display_name', align: 'left' },
  { name: 'permissions_count', label: '權限數', field: 'permissions_count', align: 'center', sortable: true },
  { name: 'is_system', label: '類型', field: 'is_system', align: 'center' },
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
  role: null as any,
})

const confirmDelete = (role: any) => {
  deleteDialog.value.role = role
  deleteDialog.value.show = true
}

const deleteRole = () => {
  router.delete(route('admin.roles.destroy', deleteDialog.value.role.id), {
    onSuccess: () => {
      deleteDialog.value.show = false
    },
  })
}
</script>
