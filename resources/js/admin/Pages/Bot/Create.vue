<template>
  <div>
    <div class="text-h5 q-mb-md">新增機器人</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">

          <q-input
            v-model="form.name"
            label="機器人名稱"
            outlined
            :error="!!form.errors.name"
            :error-message="form.errors.name"
          />

          <q-select
            v-model="form.channel"
            :options="channels"
            label="通信管道"
            outlined
            emit-value
            map-options
            :error="!!form.errors.channel"
            :error-message="form.errors.channel"
          />

          <q-input
            v-model="form.identity"
            label="身份設定（你是誰）"
            outlined
            type="textarea"
            autogrow
            hint="描述這個機器人的角色與個性"
          />

          <q-input
            v-model="form.allowed_actions"
            label="可以做的事"
            outlined
            type="textarea"
            autogrow
            hint="一行一條規則，例如：可以回答產品相關問題"
          />

          <q-input
            v-model="form.restricted_actions"
            label="不可以回答的事"
            outlined
            type="textarea"
            autogrow
            hint="一行一條規則，例如：不可回答競品比較問題"
          />

          <q-input
            v-model="form.forbidden_actions"
            label="絕對不可以做的事"
            outlined
            type="textarea"
            autogrow
            hint="一行一條規則，例如：不可假裝自己是人類"
          />

          <q-toggle
            v-model="form.is_active"
            label="啟用"
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
              @click="$inertia.visit(route('admin.ai-bots.index'))"
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

defineOptions({ layout: AdminLayout })

defineProps<{
  channels: { value: string; label: string }[]
}>()

const form = useForm({
  name: '',
  channel: 'discord',
  identity: '',
  allowed_actions: '',
  restricted_actions: '',
  forbidden_actions: '',
  is_active: true,
})

const onSubmit = () => {
  form.post(route('admin.ai-bots.store'))
}
</script>
