<template>
  <q-layout>
    <q-page-container>
      <q-page class="login-page flex flex-center">
        <q-card class="login-card" flat bordered>
          <q-card-section class="text-center q-pb-none">
            <q-icon name="fas fa-user-shield" size="48px" color="primary" />
            <div class="text-h5 q-mt-sm text-weight-bold">Comet Admin</div>
            <div class="text-body2 text-grey-6 q-mt-xs">請輸入您的帳號與密碼</div>
          </q-card-section>

          <q-card-section class="q-pt-xl q-px-xl q-pb-lg">
            <q-form @submit="onSubmit">
              <q-input
                v-model="form.account"
                label="帳號"
                outlined
                :error="!!$page.props.errors.account"
                :error-message="$page.props.errors.account"
                @update:model-value="clearError"
                class="q-mb-md"
              >
                <template #prepend>
                  <q-icon name="fas fa-user" />
                </template>
              </q-input>

              <q-input
                v-model="form.password"
                label="密碼"
                outlined
                :type="showPassword ? 'text' : 'password'"
                :error="!!$page.props.errors.account"
                @update:model-value="clearError"
                class="q-mb-sm"
              >
                <template #prepend>
                  <q-icon name="fas fa-lock" />
                </template>
                <template #append>
                  <q-icon
                    :name="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                    class="cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </q-input>

              <div class="row items-center q-mb-lg">
                <q-checkbox v-model="form.remember" label="記住我" dense color="primary" />
              </div>

              <q-btn
                type="submit"
                label="登入"
                color="primary"
                class="full-width"
                :loading="form.processing"
                unelevated
                no-caps
                size="lg"
                style="border-radius: 8px"
              />
            </q-form>
          </q-card-section>

          <q-card-section class="text-center text-caption text-grey-5 q-pt-none">
            &copy; {{ new Date().getFullYear() }} Comet Admin
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()

const form = reactive({
  account: '',
  password: '',
  remember: false,
  processing: false,
})

const showPassword = ref(false)

const clearError = () => {
  if (page.props.errors?.account) {
    page.props.errors.account = null
  }
}

const onSubmit = () => {
  form.processing = true
  router.post('/admin/login', {
    account: form.account,
    password: form.password,
    remember: form.remember,
  }, {
    onFinish: () => {
      form.processing = false
    },
  })
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #1565c0 0%, #0d47a1 50%, #1a237e 100%);
}

.login-card {
  width: 420px;
  max-width: 90vw;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}
</style>
