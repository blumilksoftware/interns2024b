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
        :value="user.firstname"
        label="Imię"
      />

      <CustomOutput
        :value="user.surname"
        label="Nazwisko"
      />

      <CustomOutput
        :value="user.email"
        label="E-mail"
      />

      <CustomOutput
        :value="user.school.name"
        label="Szkoła"
      />

      <ThemeSwitch />
    </div>
  </div>
</template>
