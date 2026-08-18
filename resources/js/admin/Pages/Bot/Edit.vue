<template>
  <div>
    <div class="text-h5 q-mb-md">編輯機器人</div>

    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-subtitle2 text-grey q-mb-xs">API 存取金鑰</div>
        <div class="row items-center q-gutter-sm">
          <code class="text-body2 bg-grey-2 q-pa-sm rounded-borders">
            {{ showApiKey ? bot.api_key : maskedApiKey }}
          </code>
          <q-btn flat dense :icon="showApiKey ? 'fas fa-eye-slash' : 'fas fa-eye'" @click="showApiKey = !showApiKey" />
          <q-btn flat dense icon="fas fa-copy" @click="copyText(bot.api_key, '已複製 API 金鑰')">
            <q-tooltip>複製金鑰</q-tooltip>
          </q-btn>
        </div>
        <q-toggle v-model="form.regenerate_api_key" label="重新產生 API 金鑰" color="warning" class="q-mt-sm" />
        <div v-if="form.regenerate_api_key" class="text-caption text-warning">
          儲存後將產生新金鑰，舊金鑰立即失效
        </div>
      </q-card-section>
    </q-card>

    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-subtitle2 text-grey q-mb-xs">Agent Instructions URL</div>
        <div class="row items-center q-gutter-sm">
          <code class="col text-body2 bg-grey-2 q-pa-sm rounded-borders ellipsis">{{ bot.instructions_url }}</code>
          <q-btn flat dense icon="fas fa-copy" @click="copyText(bot.instructions_url, '已複製 Instructions URL')">
            <q-tooltip>複製 URL</q-tooltip>
          </q-btn>
        </div>
        <div class="text-caption text-grey q-mt-sm">此 URL 僅回傳身份與行為規則的純文字設定。</div>
        <q-toggle v-model="form.regenerate_identity_token" label="重新產生 Identity Token" color="warning" class="q-mt-sm" />
        <div v-if="form.regenerate_identity_token" class="text-caption text-warning">
          儲存後將產生新 URL，舊 URL 立即失效
        </div>
      </q-card-section>
    </q-card>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input v-model="form.name" label="機器人名稱" outlined :error="!!form.errors.name" :error-message="form.errors.name" />
          <q-select v-model="form.channel" :options="channels" label="通信管道" outlined emit-value map-options :error="!!form.errors.channel" :error-message="form.errors.channel" />
          <q-input v-model="form.identity" label="身份設定（你是誰）" outlined type="textarea" autogrow hint="描述這個機器人的角色與個性" />
          <q-input v-model="form.allowed_actions" label="可以做的事" outlined type="textarea" autogrow hint="一行一條規則，例如：可以回答產品相關問題" />
          <q-input v-model="form.restricted_actions" label="不可以回答的事" outlined type="textarea" autogrow hint="一行一條規則，例如：不可回答競品比較問題" />
          <q-input v-model="form.forbidden_actions" label="絕對不可以做的事" outlined type="textarea" autogrow hint="一行一條規則，例如：不可假裝自己是人類" />
          <q-toggle v-model="form.is_active" label="啟用" />
          <div class="row q-gutter-sm">
            <q-btn type="submit" label="更新" color="primary" :loading="form.processing" />
            <q-btn label="取消" flat @click="$inertia.visit(route('admin.ai-bots.index'))" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useQuasar } from 'quasar'
import AdminLayout from '../../Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const $q = useQuasar()
const props = defineProps<{ bot: any; channels: { value: string; label: string }[] }>()
const showApiKey = ref(false)

const maskedApiKey = computed(() => {
  const key = props.bot.api_key as string
  return key.substring(0, 8) + '••••••••••••••••••••••••••••••••'
})

const copyText = async (text: string, message: string) => {
  await navigator.clipboard.writeText(text)
  $q.notify({ type: 'positive', message })
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
  regenerate_identity_token: false,
})

const onSubmit = () => form.put(route('admin.ai-bots.update', props.bot.id))
</script>
