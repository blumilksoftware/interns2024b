<script setup lang="ts">
import Divider from '@/components/Common/Divider.vue'
import CustomOutput from '@/components/Common/CustomOutput.vue'
import ThemeSwitch from '@/components/Common/ThemeSwitch.vue'

defineProps<{ user: User }>()
</script>

<template>
  <div class="flex flex-col gap-4 items-center">
    <Divider>
      <b class="text-lg text-primary duration-200 transition-colors">
        Dane o profilu
      </b>
    </Divider>

    <div class="flex flex-col gap-4 sm:max-w-96 w-full sm:min-w-96">
      <CustomOutput
        label="Imię i Nazwisko"
        :value="`${user.firstname}  ${user.surname}`"
      />

      <CustomOutput
        label="E-mail"
        :value="user.email"
      />

      <CustomOutput
        label="Szkoła"
        :value="user.school.name"
      />

      <ThemeSwitch class="mx-4 my-3" />
    </div>
  </div>
</template>
