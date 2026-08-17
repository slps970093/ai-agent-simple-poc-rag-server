<template>
  <div>
    <div class="text-h5 q-mb-md">編輯管理者</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="form.account"
            label="帳號"
            outlined
            :error="!!form.errors.account"
            :error-message="form.errors.account"
          />

          <q-input
            v-model="form.name"
            label="姓名"
            outlined
            :error="!!form.errors.name"
            :error-message="form.errors.name"
          />

          <q-input
            v-model="form.password"
            label="密碼（留空不修改）"
            outlined
            type="password"
            :error="!!form.errors.password"
            :error-message="form.errors.password"
          />

          <q-input
            v-model="form.password_confirmation"
            label="確認密碼"
            outlined
            type="password"
          />

          <div class="q-mb-sm">
            <q-badge :color="form.is_root ? 'warning' : 'grey'" class="q-pa-sm">
              <q-icon name="fas fa-shield-halved" class="q-mr-xs" />
              {{ form.is_root ? 'Root 最高權限' : '一般管理者' }}
            </q-badge>
          </div>

          <q-select
            v-model="form.role_ids"
            :options="roleOptions"
            label="角色"
            outlined
            multiple
            emit-value
            map-options
            use-chips
          />

          <q-toggle v-model="form.is_active" label="啟用" />

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
              @click="$inertia.visit(route('admin.users.index'))"
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
  user: any
  roles: any[]
}>()

const roleOptions = computed(() =>
  props.roles.map(r => ({
    label: `${r.name} - ${typeof r.display_name === 'object' ? (r.display_name.zh_TW || r.display_name.en) : r.display_name}`,
    value: r.id,
  }))
)

const form = useForm({
  account: props.user.account,
  name: props.user.profile?.name || '',
  password: '',
  password_confirmation: '',
  role_ids: props.user.role_ids || [],
  is_active: props.user.status === 'active',
})

const onSubmit = () => {
  form.put(route('admin.users.update', props.user.id))
}
</script>
