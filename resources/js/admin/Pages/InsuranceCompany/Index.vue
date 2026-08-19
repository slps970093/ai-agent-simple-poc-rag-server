<template>
  <div>
    <div class="row items-center justify-between q-mb-md"><div class="text-h5">保險公司</div><q-btn v-if="canCreate" color="primary" label="新增保險公司" icon="add" @click="$inertia.visit(route('admin.insurance-companies.create'))" /></div>
    <q-card><q-card-section><q-table :rows="companies" :columns="columns" row-key="id" flat>
      <template #body-cell-is_active="props"><q-td :props="props"><q-badge :color="props.value ? 'positive' : 'grey'">{{ props.value ? '啟用' : '停用' }}</q-badge></q-td></template>
      <template #body-cell-actions="props"><q-td :props="props"><q-btn v-if="canEdit" flat round dense icon="edit" @click="$inertia.visit(route('admin.insurance-companies.edit', props.row.id))" /><q-btn v-if="canDelete" flat round dense color="negative" icon="delete" @click="selected = props.row; showDelete = true" /></q-td></template>
    </q-table></q-card-section></q-card>
    <q-dialog v-model="showDelete"><q-card><q-card-section class="text-h6">刪除保險公司</q-card-section><q-card-section>確定要刪除「{{ selected?.name }}」嗎？</q-card-section><q-card-actions align="right"><q-btn flat label="取消" v-close-popup /><q-btn flat color="negative" label="刪除" @click="remove" /></q-card-actions></q-card></q-dialog>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'
import { useCan } from '../../Composables/useCan'
defineOptions({ layout: AdminLayout })
type Company = { id:number; code:string; name:string; is_active:boolean; policies_count:number; created_at:string }
defineProps<{ companies: Company[] }>()
const { can: canCreate } = useCan('admin.insurance-companies.create'); const { can: canEdit } = useCan('admin.insurance-companies.edit'); const { can: canDelete } = useCan('admin.insurance-companies.delete')
const showDelete = ref(false); const selected = ref<Company | null>(null)
const columns = [{ name:'code', label:'代號', field:'code', align:'left', sortable:true },{ name:'name', label:'名稱', field:'name', align:'left', sortable:true },{ name:'policies_count', label:'保單數', field:'policies_count', align:'right' },{ name:'is_active', label:'狀態', field:'is_active', align:'center' },{ name:'created_at', label:'建立時間', field:'created_at', align:'left' },{ name:'actions', label:'操作', field:'actions', align:'right' }]
const remove = () => selected.value && router.delete(route('admin.insurance-companies.destroy', selected.value.id), { onSuccess: () => { showDelete.value = false } })
</script>
