<script setup lang="ts">
defineProps<{ title: string }>()
const isVisible = defineModel<boolean>()
</script>

<template>
  <Transition>
    <div
      v-show="isVisible"
      class="inset-0 h-screen w-full bg-black/15 backdrop-blur-md fixed z-20 p-5 flex justify-center items-center"
      @click="setHidden"
    >
      <div
        class="bg-white/85 backdrop-blur-md rounded-xl w-full flex flex-col max-w-2xl"
        @click.stop
      >
        <h1
          class="p-5 font-bold text-xl outline-none focus:shadow-md duration-200"
          tabindex="0"
        >
          {{ title }}
        </h1>

        <hr>

        <p
          class="p-5 text-[1.1rem] outline-none focus:shadow-md duration-200"
          tabindex="0"
        >
          <slot />
        </p>

        <hr>

        <div class="flex justify-end p-5">
          <button
            class="font-bold text-primary"
            @click="isVisible = false"
          >
            Powrót
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
