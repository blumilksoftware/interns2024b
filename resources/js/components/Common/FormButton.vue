<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";

  const form = useForm({});

  const props = withDefaults(defineProps<{
    small?: boolean
    class?: string
    href: string,
    method?: "post" | "get" | "put" | "patch" | "delete"
    options?: object
  }>(), { method: 'get' });


  function handleSubmit() {
    form[props.method](props.href, props.options);
  }
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <button
      type="submit"
      class="bg-primary px-3 semibold text-white  font-semibold shadow-sm hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
      :class="`${small ? 'rounded-md text-sm py-2' : 'rounded-lg py-3'} ${props.class}`"
    >
      <slot />
    </button>
  </form>

</template>


