<script setup lang="ts">
import { ref } from 'vue'
import { type Errors, type VisitOptions } from '@inertiajs/core'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import ButtonFrame from '@/components/Common/ButtonFrame.vue'
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

withDefaults(
  defineProps<{ href:string, buttonClass?:string } & ButtonFrameProps & VisitOptions>(),
  { buttonClass: undefined, preserveState: true, preserveScroll: true },
)

const emit = defineEmits<{
  click: []
  processing: [processing:boolean]
  errors: [errors:Errors]
}>()

const processing = ref(false)

function onProcessing(isProcessing:boolean) {
  processing.value = isProcessing
  emit('processing', isProcessing)
}
</script>

<template>
  <RequestWrapper
    :href="href"
    :method="method"
    :data="data"
    :replace="replace"
    :headers="headers"
    :only="only"
    :preserve-scroll="preserveScroll"
    :preserve-state="preserveState"
    :query-string-array-format="queryStringArrayFormat"
    :except="except"
    :error-bag="errorBag"
    :force-form-data="forceFormData"
    :disabled="disabled"
    @cancel-token="onCancelToken"
    @before="onBefore"
    @start="onStart"
    @progress="onProgress"
    @cancel="onCancel"
    @finish="onFinish"
    @success="onSuccess"
    @error="onError"
    @processing="onProcessing"
    @errors="errors => emit('errors', errors)"
    @click="emit('click')"
  >
    <ButtonFrame
      :disabled="disabled || processing"
      :class="buttonClass"
      :small :extra-small :text :icon
    >
      <slot />
    </ButtonFrame>
  </RequestWrapper>
</template>
