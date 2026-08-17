<template>
  <div>
    <div class="text-h5 q-mb-md">儀表板</div>
    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6">管理者數量</div>
            <div class="text-h3 text-primary">-</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6">選單數量</div>
            <div class="text-h3 text-primary">-</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6">系統狀態</div>
            <div class="text-h3 text-positive">正常</div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdminLayout from '../Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})
</script>
