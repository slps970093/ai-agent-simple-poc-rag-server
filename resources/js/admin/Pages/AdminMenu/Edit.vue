<template>
  <div>
    <div class="text-h5 q-mb-md">編輯選單</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="form.name.zh_TW"
            label="名稱（繁體中文）"
            outlined
            :error="!!form.errors['name.zh_TW']"
            :error-message="form.errors['name.zh_TW']"
          />

          <q-input
            v-model="form.name.en"
            label="名稱（English）"
            outlined
            :error="!!form.errors['name.en']"
            :error-message="form.errors['name.en']"
          />

          <q-select
            v-model="form.parent_id"
            :options="parentOptions"
            label="上層選單（留空為頂層）"
            outlined
            clearable
            emit-value
            map-options
          />

          <q-input
            v-model="form.icon"
            label="圖示（Font Awesome）"
            outlined
            hint="例如：fas fa-gauge, fas fa-gear, fas fa-users"
          >
            <template #append>
              <q-icon v-if="form.icon" :name="form.icon" size="24px" />
            </template>
          </q-input>

          <q-input
            v-model="form.route_name"
            label="Route Name"
            outlined
            hint="例如：admin.users.index"
          />

          <q-input
            v-model.number="form.sort_order"
            label="排序（數字越小越前面）"
            outlined
            type="number"
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
              @click="$inertia.visit(route('admin.menus.index'))"
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
  menu: any
  parents: any[]
}>()

const parentOptions = computed(() =>
  props.parents.map(p => ({
    label: typeof p.name === 'object' ? (p.name.zh_TW || p.name.en) : p.name,
    value: p.id,
  }))
)

const form = useForm({
  name: {
    zh_TW: props.menu.name?.zh_TW || '',
    en: props.menu.name?.en || '',
  },
  parent_id: props.menu.parent_id,
  icon: props.menu.icon || '',
  route_name: props.menu.route_name || '',
  sort_order: props.menu.sort_order || 0,
  is_active: props.menu.is_active,
})

const onSubmit = () => {
  form.put(route('admin.menus.update', props.menu.id))
}
</script>
