import { nanoid } from 'nanoid'
import { inject, provide, ref } from 'vue'
import { type Errors } from '@inertiajs/core'

interface RequestResolutionEmitter {
  emitRequestProcessing: (processing: boolean) => void
  emitRequestErrors: (processing: Errors) => void
}

const currentKey = nanoid()

export default function useRequestResolution() {
  const resolution = { processing: ref(false), errors: ref<Errors>({}) }

  provide<RequestResolutionEmitter>(
    currentKey, 
    { 
      emitRequestProcessing: (processing: boolean) => resolution.processing.value = processing,
      emitRequestErrors: (errors: Errors) => resolution.errors.value = errors,
    },
  )

  return resolution
}

export function injectRequestResolutionEmitter(){
  return inject<RequestResolutionEmitter | undefined>(currentKey, undefined)
}
