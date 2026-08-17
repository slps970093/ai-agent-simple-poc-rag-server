<template>
  <div>
    <div class="text-h5 q-mb-md">編輯機器人</div>

    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-subtitle2 text-grey q-mb-xs">API 存取金鑰</div>
        <div class="row items-center q-gutter-sm">
          <code class="text-body2 bg-grey-2 q-pa-sm rounded-borders">
            {{ showKey ? bot.api_key : maskedKey }}
          </code>
          <q-btn
            flat
            dense
            :icon="showKey ? 'fas fa-eye-slash' : 'fas fa-eye'"
            @click="showKey = !showKey"
          />
          <q-btn
            flat
            dense
            icon="fas fa-copy"
            @click="copyKey"
          >
            <q-tooltip>複製金鑰</q-tooltip>
          </q-btn>
        </div>
        <q-toggle
          v-model="form.regenerate_api_key"
          label="重新產生 API 金鑰"
          color="warning"
          class="q-mt-sm"
        />
        <div v-if="form.regenerate_api_key" class="text-caption text-warning">
          儲存後將產生新金鑰，舊金鑰立即失效
        </div>
      </q-card-section>
    </q-card>

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
              label="更新"
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
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useQuasar } from 'quasar'
import AdminLayout from '../../Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const $q = useQuasar()

const props = defineProps<{
  bot: any
  channels: { value: string; label: string }[]
}>()

const showKey = ref(false)

const maskedKey = computed(() => {
  const key = props.bot.api_key as string
  return key.substring(0, 8) + '••••••••••••••••••••••••••••••••'
})

const copyKey = () => {
  navigator.clipboard.writeText(props.bot.api_key)
  $q.notify({ type: 'positive', message: '已複製金鑰' })
}

const form = useForm({
  name: props.bot.name,
  channel: props.bot.channel,
  identity: props.bot.identity ?? '',
  allowed_actions: props.bot.allowed_actions ?? '',
  restricted_actions: props.bot.restricted_actions ?? '',
  forbidden_actions: props.bot.forbidden_actions ?? '',
  is_active: props.bot.is_active,
  regenerate_api_key: false,
})

const onSubmit = () => {
  form.put(route('admin.ai-bots.update', props.bot.id))
}
</script>
