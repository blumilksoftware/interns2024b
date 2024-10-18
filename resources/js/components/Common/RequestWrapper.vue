<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { type Errors, type ActiveVisit, type VisitOptions, type PendingVisit } from '@inertiajs/core'
import { injectRequestResolutionEmitter } from '@/Helpers/RequestResolution'

const resolution = injectRequestResolutionEmitter()
const props = defineProps<VisitOptions & { href:string, disabled?:boolean, type?:'button'|'submit'|'reset' }>()
const processing = ref(false)
const emit = defineEmits<{ processing: [processing:boolean], errors: [errors:Errors] }>()

function onRequestStart(pending: PendingVisit) {
  if (props.onStart) props.onStart(pending)
  processing.value = true
  emit('processing', processing.value)
  emit('errors', {})
  if (!resolution) return
  resolution.emitRequestProcessing(processing.value)
  resolution.emitRequestErrors({})
}

function onRequestError(errors:Errors){
  if (props.onError) props.onError(errors)
  processing.value = false
  emit('errors', errors)
  if (!resolution) return
  resolution.emitRequestErrors(errors)
}

function onRequestFinish(finish:ActiveVisit) {
  if (props.onFinish) props.onFinish(finish)
  processing.value = false
  emit('processing', processing.value)
  if (!resolution) return
  resolution.emitRequestProcessing(processing.value)
}

function visit() {
  router.visit(
    props.href, {
      method: props.method ?? 'get',
      data: props.data,
      replace: props.replace,
      headers: props.headers,
      only: props.only,
      preserveScroll: props.preserveScroll,
      preserveState: props.preserveState,
      queryStringArrayFormat: props.queryStringArrayFormat,
      except: props.except,
      errorBag: props.errorBag,
      forceFormData: props.forceFormData,
      onCancelToken: props.onCancelToken,
      onBefore: props.onBefore,
      onStart: onRequestStart,
      onProgress: props.onProgress,
      onCancel: props.onCancel,
      onFinish: onRequestFinish,
      onSuccess: props.onSuccess,
      onError: onRequestError,
    },
  )
}
</script>

<template>
  <button
    :type="type ?? 'submit'"
    :disabled="disabled || processing"
    @click="visit"
  >
    <slot />
  </button>
</template>
