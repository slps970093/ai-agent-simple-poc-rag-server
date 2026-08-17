<template>
  <div>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">管理者列表</div>
      <q-btn
        color="primary"
        label="新增管理者"
        icon="fas fa-plus"
        @click="$inertia.visit(route('admin.users.create'))"
      />
    </div>

    <q-card>
      <q-table
        :rows="users"
        :columns="columns"
        row-key="id"
        flat
        bordered
      >
        <template #body-cell-status="props">
          <q-td :props="props">
            <q-badge :color="props.row.status === 'active' ? 'positive' : 'negative'">
              {{ props.row.status === 'active' ? '啟用' : '停用' }}
            </q-badge>
          </q-td>
        </template>

        <template #body-cell-is_root="props">
          <q-td :props="props">
            <q-badge v-if="props.row.is_root" color="warning">Root</q-badge>
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
              @click="$inertia.visit(route('admin.users.edit', props.row.id))"
            />
            <q-btn
              v-if="!props.row.is_root"
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
          確定要刪除管理者「{{ deleteDialog.user?.account }}」嗎？
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="取消" v-close-popup />
          <q-btn color="negative" label="刪除" @click="deleteUser" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps<{
  users: any[]
}>()

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'account', label: '帳號', field: 'account', align: 'left', sortable: true },
  { name: 'name', label: '姓名', field: row => row.profile?.name || '-', align: 'left' },
  { name: 'is_root', label: '權限', field: 'is_root', align: 'center' },
  { name: 'status', label: '狀態', field: 'status', align: 'center' },
  { name: 'last_login_at', label: '最後登入', field: 'last_login_at', align: 'left' },
  { name: 'actions', label: '操作', field: 'actions', align: 'center' },
]

const deleteDialog = ref({
  show: false,
  user: null as any,
})

const confirmDelete = (user: any) => {
  deleteDialog.value.user = user
  deleteDialog.value.show = true
}

const deleteUser = () => {
  router.delete(route('admin.users.destroy', deleteDialog.value.user.id), {
    onSuccess: () => {
      deleteDialog.value.show = false
    },
  })
}
</script>
