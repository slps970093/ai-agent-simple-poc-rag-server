<template>
  <div>
    <div class="text-h5 q-mb-md">新增角色</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="form.display_name.zh_TW"
            label="顯示名稱（繁體中文）"
            outlined
            :error="!!form.errors['display_name.zh_TW']"
            :error-message="form.errors['display_name.zh_TW']"
          />

          <q-input
            v-model="form.display_name.en"
            label="顯示名稱（English）"
            outlined
            :error="!!form.errors['display_name.en']"
            :error-message="form.errors['display_name.en']"
          />

          <q-input
            v-model="form.description.zh_TW"
            label="描述（繁體中文）"
            outlined
            type="textarea"
          />

          <q-input
            v-model="form.description.en"
            label="描述（English）"
            outlined
            type="textarea"
          />

          <q-select
            v-model="form.permissions"
            :options="permissionOptions"
            label="權限"
            outlined
            multiple
            emit-value
            map-options
            use-chips
          />

          <div class="row q-gutter-sm">
            <q-btn
              type="submit"
              label="建立"
              color="primary"
              :loading="form.processing"
            />
            <q-btn
              label="取消"
              flat
              @click="$inertia.visit(route('admin.roles.index'))"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminLayout from '../../Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps<{
  permissions: any[]
}>()

const permissionOptions = computed(() =>
  props.permissions.map(p => ({
    label: `admin.${p.module}.${p.action} - ${typeof p.display_name === 'object' ? (p.display_name.zh_TW || p.display_name.en) : p.display_name}`,
    value: p.id,
  }))
)

const form = useForm({
  display_name: { zh_TW: '', en: '' },
  description: { zh_TW: '', en: '' },
  permissions: [],
})

const onSubmit = () => {
  form.post(route('admin.roles.store'))
}
</script>
