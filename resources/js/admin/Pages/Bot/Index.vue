<template>
  <div>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">機器人管理</div>
      <q-btn
        v-if="can('admin.ai-bots.create')"
        color="primary"
        label="新增機器人"
        icon="fas fa-plus"
        @click="$inertia.visit(route('admin.ai-bots.create'))"
      />
    </div>

    <q-card>
      <q-table
        :rows="bots"
        :columns="columns"
        row-key="id"
        flat
        bordered
      >
        <template #body-cell-channel="props">
          <q-td :props="props">
            <q-badge color="blue-grey">{{ props.row.channel_label }}</q-badge>
          </q-td>
        </template>

        <template #body-cell-status="props">
          <q-td :props="props">
            <q-badge :color="props.row.status === 'active' ? 'positive' : 'grey'">
              {{ props.row.status === 'active' ? '啟用' : '停用' }}
            </q-badge>
          </q-td>
        </template>

        <template #body-cell-actions="props">
          <q-td :props="props">
            <q-btn
              v-if="can('admin.ai-bots.edit')"
              flat
              round
              dense
              icon="fas fa-pen"
              color="primary"
              @click="$inertia.visit(route('admin.ai-bots.edit', props.row.id))"
            />
            <q-btn
              v-if="can('admin.ai-bots.delete')"
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
          確定要刪除機器人「{{ deleteDialog.bot?.name }}」嗎？此操作為軟刪除，資料仍可還原。
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="取消" v-close-popup />
          <q-btn color="negative" label="刪除" @click="deleteBot" />
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

defineOptions({ layout: AdminLayout })

const { can } = useCan()

defineProps<{
  bots: any[]
}>()

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const, sortable: true },
  { name: 'name', label: '名稱', field: 'name', align: 'left' as const, sortable: true },
  { name: 'channel', label: '通信管道', field: 'channel', align: 'center' as const },
  { name: 'status', label: '狀態', field: 'status', align: 'center' as const },
  { name: 'created_at', label: '建立時間', field: 'created_at', align: 'left' as const, sortable: true },
  { name: 'actions', label: '操作', field: 'actions', align: 'center' as const },
]

const deleteDialog = ref({
  show: false,
  bot: null as any,
})

const confirmDelete = (bot: any) => {
  deleteDialog.value.bot = bot
  deleteDialog.value.show = true
}

const deleteBot = () => {
  router.delete(route('admin.ai-bots.destroy', deleteDialog.value.bot.id), {
    onSuccess: () => {
      deleteDialog.value.show = false
    },
  })
}
</script>
