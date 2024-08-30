<script setup lang="ts">

const props = defineProps<{ isVisible: boolean }>()
const emit = defineEmits<(e: 'update:isVisible', value: boolean) => void>()
function setHidden() {
  emit('update:isVisible', false)
}
</script>

<template>
  <Transition>
    <div v-show="props.isVisible"
         class="inset-0 h-screen w-full bg-black/15 backdrop-blur-md fixed z-20 p-5 flex justify-center items-center" @click="setHidden"
    >
      <div class="bg-white/85 backdrop-blur-md rounded-xl w-full flex flex-col max-w-2xl" @click.stop>
        <h1 class="p-5 font-bold text-xl">Title</h1>
        <hr>
        <p class="p-5 text-[1.1rem]">
          Lorem ipsum dolor sit amet consectetur adipisicing elit.
          Esse corrupti provident voluptas ducimus neque libero reprehenderit eos voluptate quisquam minus.
          Dolorum quos animi fugit blanditiis repellendus voluptas sequi ex ea.
        </p>
        <hr>
        <div class="flex justify-end p-5">
          <button class="font-bold text-primary" @click="setHidden">Powrót</button>
        </div>
      </div>
    </div>
  </Transition>
</template>
