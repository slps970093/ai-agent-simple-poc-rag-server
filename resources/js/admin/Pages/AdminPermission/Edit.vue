<template>
  <div>
    <div class="text-h5 q-mb-md">編輯權限</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <div class="row q-col-gutter-md">
            <div class="col-6">
              <q-input
                v-model="form.module"
                label="模組名"
                outlined
                :error="!!form.errors.module"
                :error-message="form.errors.module"
              />
            </div>
            <div class="col-6">
              <q-input
                v-model="form.action"
                label="操作"
                outlined
                :error="!!form.errors.action"
                :error-message="form.errors.action"
              />
            </div>
          </div>

          <q-banner class="bg-grey-3 q-mb-sm">
            識別碼：<strong>admin.{{ form.module }}.{{ form.action }}</strong>
          </q-banner>

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

          <div class="row q-gutter-sm">
            <q-btn
              type="submit"
              label="更新"
              color="primary"
              :loading="form.processing"
            />
            <q-btn
              label="取消"
              flat
              @click="$inertia.visit(route('admin.permissions.index'))"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps<{
  permission: any
}>()

const form = useForm({
  module: props.permission.module,
  action: props.permission.action,
  display_name: {
    zh_TW: props.permission.display_name?.zh_TW || '',
    en: props.permission.display_name?.en || '',
  },
})

const onSubmit = () => {
  form.put(route('admin.permissions.update', props.permission.id))
}
</script>
